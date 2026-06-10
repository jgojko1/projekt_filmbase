<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$stats = [];
foreach (['users' => 'Users', 'news' => 'News', 'gallery' => 'Gallery', 'contacts' => 'Messages'] as $table => $label) {
    $r = $conn->query("SELECT COUNT(*) AS cnt FROM `$table`");
    $stats[$label] = $r ? (int)$r->fetch_assoc()['cnt'] : 0;
}

$recentUsers = $conn->query(
    "SELECT id, firstname, lastname, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5"
)->fetch_all(MYSQLI_ASSOC);

$recentNews = $conn->query(
    "SELECT n.id, n.title, n.created_at, u.firstname, u.lastname
     FROM news n LEFT JOIN users u ON u.id = n.user_id
     ORDER BY n.created_at DESC LIMIT 5"
)->fetch_all(MYSQLI_ASSOC);

$recentGallery = $conn->query(
    "SELECT id, title, image, created_at FROM gallery ORDER BY created_at DESC LIMIT 5"
)->fetch_all(MYSQLI_ASSOC);

$hnbRates = [];
$hnbError = '';
$hnbXml   = '';
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => 'https://api.hnb.hr/tecajn-eur/v3',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 8,
    CURLOPT_USERAGENT      => 'FilmBase/1.0',
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
]);
$hnbResponse = curl_exec($ch);
$hnbCurlErr  = curl_error($ch);
curl_close($ch);

if ($hnbCurlErr) {
    $hnbError = 'cURL error: ' . $hnbCurlErr;
} elseif ($hnbResponse) {
    $decoded = json_decode($hnbResponse, true);
    if (is_array($decoded)) {
        $hnbRates = array_slice($decoded, 0, 10);

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><exchange_rates/>');
        $xml->addAttribute('source', 'HNB');
        $xml->addAttribute('base', 'EUR');
        $xml->addAttribute('generated', date('Y-m-d H:i:s'));
        foreach ($decoded as $rate) {
            $item = $xml->addChild('currency');
            $item->addChild('code',    htmlspecialchars($rate['valuta']        ?? ''));
            $item->addChild('name',    htmlspecialchars($rate['naziv_valute']  ?? ''));
            $item->addChild('unit',    htmlspecialchars($rate['jedinica']      ?? ''));
            $item->addChild('buying',  htmlspecialchars($rate['kupovni_tecaj'] ?? ''));
            $item->addChild('middle',  htmlspecialchars($rate['srednji_tecaj'] ?? ''));
            $item->addChild('selling', htmlspecialchars($rate['prodajni_tecaj']?? ''));
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $hnbXml = $dom->saveXML();
    } else {
        $hnbError = 'Could not parse HNB API response.';
    }
}

$user = currentUser();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">

    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <h3>Admin Panel</h3>
        </div>
        <nav class="admin-nav">
            <a href="/filmbase/admin/index.php"       class="active"><span class="nav-icon">&#128202;</span> Dashboard</a>
            <a href="/filmbase/admin/users.php">       <span class="nav-icon">&#128101;</span> Users</a>
            <a href="/filmbase/admin/news-add.php">    <span class="nav-icon">&#128221;</span> Add News</a>
            <a href="/filmbase/admin/gallery-add.php"> <span class="nav-icon">&#128247;</span> Add to Gallery</a>
            <a href="/filmbase/index.php" style="margin-top:1rem; border-top:1px solid var(--border); padding-top:1rem;">
                <span class="nav-icon">&#8592;</span> Back to Site
            </a>
        </nav>
    </aside>

    <div class="admin-content">

        <div class="admin-header">
            <div>
                <h1 class="admin-title">Dashboard</h1>
                <p style="color:var(--text-muted); font-size:0.9rem; margin:0;">
                    Welcome back, <?php echo e($user['firstname']); ?>. Here's what's happening.
                </p>
            </div>
            <span style="color:var(--text-muted); font-size:0.85rem;">
                <?php echo date('l, d M Y'); ?>
            </span>
        </div>

        <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Article deleted successfully.</div><?php endif; ?>

        <div class="admin-stats">
            <?php
            $icons = ['Users' => '&#128101;', 'News' => '&#128240;', 'Gallery' => '&#128247;', 'Messages' => '&#128231;'];
            foreach ($stats as $label => $count):
            ?>
                <div class="stat-card">
                    <div style="font-size:1.8rem; margin-bottom:0.5rem;"><?php echo $icons[$label]; ?></div>
                    <div class="stat-number"><?php echo $count; ?></div>
                    <div class="stat-label"><?php echo e($label); ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="tab-nav">
            <button class="tab-btn active" data-tab="users">   Users</button>
            <button class="tab-btn"        data-tab="news">    News</button>
            <button class="tab-btn"        data-tab="gallery"> Gallery</button>
            <button class="tab-btn"        data-tab="hnb-xml"> HNB XML</button>
            <button class="tab-btn"        data-tab="hnb-json">HNB JSON</button>
        </div>

        <div id="tab-users" class="tab-panel">
            <div class="admin-table-card">
                <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem 1.25rem; border-bottom:1px solid var(--border);">
                    <h3 style="font-size:1rem; margin:0;">Recent Users</h3>
                    <a href="/filmbase/admin/users.php" class="btn btn-outline btn-sm">View All</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $u): ?>
                            <tr>
                                <td><?php echo (int)$u['id']; ?></td>
                                <td><?php echo e($u['firstname'] . ' ' . $u['lastname']); ?></td>
                                <td><?php echo e($u['email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo e($u['role']); ?>">
                                        <?php echo e($u['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo e(date('d M Y', strtotime($u['created_at']))); ?></td>
                                <td>
                                    <a href="/filmbase/admin/users.php?edit=<?php echo (int)$u['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-news" class="tab-panel">
            <div class="admin-table-card">
                <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem 1.25rem; border-bottom:1px solid var(--border);">
                    <h3 style="font-size:1rem; margin:0;">Recent News</h3>
                    <a href="/filmbase/admin/news-add.php" class="btn btn-primary btn-sm">+ Add Article</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr><th>#</th><th>Title</th><th>Author</th><th>Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentNews as $n): ?>
                            <tr>
                                <td><?php echo (int)$n['id']; ?></td>
                                <td><?php echo e(mb_substr($n['title'], 0, 50)) . (mb_strlen($n['title']) > 50 ? '…' : ''); ?></td>
                                <td><?php echo e($n['firstname'] . ' ' . $n['lastname']); ?></td>
                                <td><?php echo e(date('d M Y', strtotime($n['created_at']))); ?></td>
                                <td style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                    <a href="/filmbase/admin/news-edit.php?id=<?php echo (int)$n['id']; ?>"   class="btn btn-outline btn-sm">Edit</a>
                                    <a href="/filmbase/admin/news-delete.php?id=<?php echo (int)$n['id']; ?>" class="btn btn-danger btn-sm"
                                       data-confirm="Delete this article?">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-gallery" class="tab-panel">
            <div class="admin-table-card">
                <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem 1.25rem; border-bottom:1px solid var(--border);">
                    <h3 style="font-size:1rem; margin:0;">Recent Gallery Images</h3>
                    <a href="/filmbase/admin/gallery-add.php" class="btn btn-primary btn-sm">+ Upload Image</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr><th>#</th><th>Preview</th><th>Title</th><th>Uploaded</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentGallery as $g): ?>
                            <tr>
                                <td><?php echo (int)$g['id']; ?></td>
                                <td>
                                    <?php if (!empty($g['image'])): ?>
                                        <img src="/filmbase/assets/uploads/<?php echo e($g['image']); ?>"
                                             alt="<?php echo e($g['title']); ?>"
                                             style="width:60px; height:42px; object-fit:cover; border-radius:4px; border:1px solid var(--border);"
                                             onerror="this.style.display='none';">
                                    <?php else: ?>
                                        <span style="color:var(--text-muted);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($g['title']); ?></td>
                                <td><?php echo e(date('d M Y', strtotime($g['created_at']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-hnb-xml" class="tab-panel">
            <div class="admin-table-card" style="padding:1.25rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <h3 style="font-size:1rem; margin:0;">HNB Exchange Rates — XML Format</h3>
                    <?php if ($hnbXml): ?>
                        <span style="font-size:0.8rem; color:var(--success);">&#10003; Live data</span>
                    <?php endif; ?>
                </div>
                <?php if ($hnbError): ?>
                    <div class="alert alert-error"><?php echo e($hnbError); ?></div>
                <?php elseif ($hnbXml): ?>
                    <pre class="xml-block"><?php echo htmlspecialchars($hnbXml, ENT_QUOTES, 'UTF-8'); ?></pre>
                <?php endif; ?>
            </div>
        </div>

        <div id="tab-hnb-json" class="tab-panel">
            <div class="admin-table-card" style="padding:1.25rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <h3 style="font-size:1rem; margin:0;">HNB Exchange Rates — JSON Format</h3>
                    <?php if (!empty($hnbRates)): ?>
                        <span style="font-size:0.8rem; color:var(--success);">&#10003; Live data</span>
                    <?php endif; ?>
                </div>
                <?php if ($hnbError): ?>
                    <div class="alert alert-error"><?php echo e($hnbError); ?></div>
                <?php elseif (!empty($hnbRates)): ?>
                    <pre class="xml-block"><?php echo htmlspecialchars(json_encode(array_slice(json_decode($hnbResponse, true), 0, 5), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?></pre>
                    <p style="color:var(--text-muted); font-size:0.82rem; margin-top:0.75rem;">
                        Showing first 5 of <?php echo count(json_decode($hnbResponse, true)); ?> currencies.
                        <a href="/filmbase/api-hnb.php">View full table &rarr;</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<style>
.xml-block {
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.25rem;
    font-size: 0.78rem;
    line-height: 1.6;
    overflow-x: auto;
    color: #a8c7fa;
    white-space: pre;
    max-height: 500px;
    overflow-y: auto;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

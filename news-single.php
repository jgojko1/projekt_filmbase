<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: /filmbase/news.php');
    exit;
}

$stmt = $conn->prepare(
    "SELECT n.id, n.title, n.body, n.image, n.created_at,
            u.firstname, u.lastname
     FROM news n
     LEFT JOIN users u ON u.id = n.user_id
     WHERE n.id = ?
     LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();

if (!$article) {
    header('Location: /filmbase/news.php');
    exit;
}

$pageTitle = $article['title'];

$relStmt = $conn->prepare("SELECT id, title, image, created_at FROM news WHERE id != ? ORDER BY created_at DESC LIMIT 3");
$relStmt->bind_param('i', $id);
$relStmt->execute();
$related = $relStmt->get_result()->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <div class="container">
        <p class="breadcrumb">
            <a href="/filmbase/index.php">Home</a> &rsaquo;
            <a href="/filmbase/news.php">News</a> &rsaquo;
            <?php echo e(mb_substr($article['title'], 0, 50)) . (mb_strlen($article['title']) > 50 ? '...' : ''); ?>
        </p>
        <h1><?php echo e($article['title']); ?></h1>
        <p class="article-meta">
            &#128197; <?php echo e(date('d M Y', strtotime($article['created_at']))); ?>
            &nbsp;&bull;&nbsp;
            &#9997; <?php echo e($article['firstname'] . ' ' . $article['lastname']); ?>
        </p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="single-layout">

            <article class="news-article">
                <?php if (!empty($article['image'])): ?>
                    <img
                        class="article-image"
                        src="/filmbase/assets/uploads/<?php echo e($article['image']); ?>"
                        alt="<?php echo e($article['title']); ?>"
                        onerror="this.style.display='none';"
                    >
                <?php endif; ?>

                <div class="article-body">
                    <?php
                    $body = $article['body'];
                    if (strpos($body, '<p') === false) {
                        $paragraphs = array_filter(array_map('trim', explode("\n\n", $body)));
                        foreach ($paragraphs as $para) {
                            echo '<p>' . e($para) . '</p>';
                        }
                    } else {
                        echo $body;
                    }
                    ?>
                </div>

                <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border);">
                    <a href="/filmbase/news.php" class="btn btn-outline">&larr; Back to News</a>
                </div>
            </article>

            <?php if (!empty($related)): ?>
                <aside class="article-sidebar">
                    <h3 class="sidebar-title">More News</h3>
                    <div class="related-list">
                        <?php foreach ($related as $rel): ?>
                            <a href="/filmbase/news-single.php?id=<?php echo (int)$rel['id']; ?>" class="related-card">
                                <div class="related-img">
                                    <?php if (!empty($rel['image'])): ?>
                                        <img
                                            src="/filmbase/assets/uploads/<?php echo e($rel['image']); ?>"
                                            alt="<?php echo e($rel['title']); ?>"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >
                                        <div class="card-img-placeholder" style="display:none; font-size:1.5rem;">&#127909;</div>
                                    <?php else: ?>
                                        <div class="card-img-placeholder" style="font-size:1.5rem;">&#127909;</div>
                                    <?php endif; ?>
                                </div>
                                <div class="related-info">
                                    <p class="related-title"><?php echo e($rel['title']); ?></p>
                                    <p class="related-date">&#128197; <?php echo e(date('d M Y', strtotime($rel['created_at']))); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>
</section>

<style>
.single-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2.5rem;
    align-items: start;
}
.sidebar-title {
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--accent);
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--border);
}
.related-list { display: flex; flex-direction: column; gap: 1rem; }
.related-card {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.75rem;
    color: var(--text-primary);
    transition: border-color 0.25s ease;
}
.related-card:hover { border-color: var(--accent); color: var(--text-primary); }
.related-img {
    width: 72px;
    height: 52px;
    flex-shrink: 0;
    border-radius: 4px;
    overflow: hidden;
    background: var(--bg-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
}
.related-img img { width: 100%; height: 100%; object-fit: cover; }
.related-title { font-size: 0.85rem; font-weight: 600; margin: 0 0 0.25rem; line-height: 1.3; }
.related-date  { font-size: 0.75rem; color: var(--text-muted); margin: 0; }

@media (max-width: 900px) {
    .single-layout { grid-template-columns: 1fr; }
    .article-sidebar { order: -1; }
    .related-list { flex-direction: row; flex-wrap: wrap; }
    .related-card { flex: 1; min-width: 200px; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

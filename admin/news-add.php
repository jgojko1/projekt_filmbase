<?php
$pageTitle = 'Add News Article';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = '';
$error   = '';
$fields  = ['title' => '', 'body' => ''];

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$maxSize      = 3 * 1024 * 1024;
$uploadDir    = __DIR__ . '/../assets/uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields['title'] = trim($_POST['title'] ?? '');
    $fields['body']  = trim($_POST['body']  ?? '');
    $user            = currentUser();
    $imageName       = '';

    if (empty($fields['title'])) {
        $error = 'Article title is required.';
    } elseif (empty($fields['body'])) {
        $error = 'Article body is required.';
    } else {
        if (!empty($_FILES['image']['name'])) {
            $file     = $_FILES['image'];
            $fileType = mime_content_type($file['tmp_name']);
            $fileSize = $file['size'];
            $fileExt  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Image upload error. Please try again.';
            } elseif (!in_array($fileType, $allowedTypes)) {
                $error = 'Only JPG, PNG, WebP, and GIF images are allowed.';
            } elseif ($fileSize > $maxSize) {
                $error = 'Image must be smaller than 3 MB.';
            } else {
                $imageName = uniqid('news_', true) . '.' . $fileExt;
                if (!move_uploaded_file($file['tmp_name'], $uploadDir . $imageName)) {
                    $error     = 'Could not save the uploaded image. Check folder permissions.';
                    $imageName = '';
                }
            }
        }

        if (empty($error)) {
            $userId = (int)$user['id'];
            $stmt   = $conn->prepare(
                "INSERT INTO news (user_id, title, body, image) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param('isss', $userId, $fields['title'], $fields['body'], $imageName);

            if ($stmt->execute()) {
                $newId   = $conn->insert_id;
                $success = 'Article published successfully!';
                $fields  = ['title' => '', 'body' => ''];
            } else {
                $error = 'Database error. Could not save the article.';
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">

    <aside class="admin-sidebar">
        <div class="admin-sidebar-header"><h3>Admin Panel</h3></div>
        <nav class="admin-nav">
            <a href="/filmbase/admin/index.php">          <span class="nav-icon">&#128202;</span> Dashboard</a>
            <a href="/filmbase/admin/users.php">          <span class="nav-icon">&#128101;</span> Users</a>
            <a href="/filmbase/admin/news-add.php" class="active"><span class="nav-icon">&#128221;</span> Add News</a>
            <a href="/filmbase/admin/gallery-add.php">    <span class="nav-icon">&#128247;</span> Add to Gallery</a>
            <a href="/filmbase/index.php" style="margin-top:1rem; border-top:1px solid var(--border); padding-top:1rem;">
                <span class="nav-icon">&#8592;</span> Back to Site
            </a>
        </nav>
    </aside>

    <div class="admin-content">

        <div class="admin-header">
            <div>
                <h1 class="admin-title">Add News Article</h1>
                <p style="color:var(--text-muted); font-size:0.9rem; margin:0;">
                    New articles are published immediately and appear on the news page.
                </p>
            </div>
            <a href="/filmbase/news.php" class="btn btn-outline btn-sm" target="_blank">View News Page &rarr;</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo e($success); ?>
                <?php if (!empty($newId)): ?>
                    &nbsp;<a href="/filmbase/news-single.php?id=<?php echo (int)$newId; ?>" target="_blank">View article &rarr;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="form-card" style="max-width:100%;">
            <form method="POST" action="/filmbase/admin/news-add.php" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="title">Article Title <span style="color:var(--accent)">*</span></label>
                    <input type="text" id="title" name="title"
                           value="<?php echo e($fields['title']); ?>"
                           placeholder="Enter a clear, descriptive headline"
                           required maxlength="255" autofocus>
                </div>

                <div class="form-group">
                    <label for="body">Article Body <span style="color:var(--accent)">*</span></label>
                    <textarea id="body" name="body" rows="16"
                              placeholder="Write the full article content here. Separate paragraphs with a blank line."
                              required><?php echo e($fields['body']); ?></textarea>
                    <p style="font-size:0.8rem; color:var(--text-muted); margin-top:0.4rem;">
                        Separate paragraphs with a blank line. Minimum recommended length: 200 characters.
                    </p>
                </div>

                <div class="form-group">
                    <label for="image">Featured Image <span style="color:var(--text-muted); font-weight:400;">(optional — JPG, PNG, WebP, GIF, max 3 MB)</span></label>
                    <input type="file" id="image" name="image"
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           data-preview="img-preview">
                    <div style="margin-top:0.75rem;">
                        <img id="img-preview"
                             src=""
                             alt="Image preview"
                             style="display:none; max-width:320px; max-height:200px; object-fit:cover; border-radius:var(--radius); border:1px solid var(--border);">
                    </div>
                </div>

                <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.5rem;">
                    <button type="submit" class="btn btn-primary">Publish Article</button>
                    <a href="/filmbase/admin/index.php" class="btn btn-outline">Cancel</a>
                </div>

            </form>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

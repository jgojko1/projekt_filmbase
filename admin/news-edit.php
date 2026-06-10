<?php
$pageTitle = 'Edit News Article';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = '';
$error   = '';

$id = (int)($_GET['id'] ?? $_POST['article_id'] ?? 0);

if ($id <= 0) {
    header('Location: /filmbase/admin/index.php');
    exit;
}

$stmt = $conn->prepare("SELECT id, title, body, image FROM news WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();

if (!$article) {
    header('Location: /filmbase/admin/index.php');
    exit;
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$maxSize      = 3 * 1024 * 1024;
$uploadDir    = __DIR__ . '/../assets/uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $body  = trim($_POST['body']  ?? '');

    if (empty($title)) {
        $error = 'Article title is required.';
    } elseif (empty($body)) {
        $error = 'Article body is required.';
    } else {
        $imageName = $article['image'];

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
                $newName = uniqid('news_', true) . '.' . $fileExt;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                    if (!empty($article['image']) && file_exists($uploadDir . $article['image'])) {
                        unlink($uploadDir . $article['image']);
                    }
                    $imageName = $newName;
                } else {
                    $error = 'Could not save the uploaded image. Check folder permissions.';
                }
            }
        }

        if (isset($_POST['remove_image']) && empty($_FILES['image']['name'])) {
            if (!empty($article['image']) && file_exists($uploadDir . $article['image'])) {
                unlink($uploadDir . $article['image']);
            }
            $imageName = '';
        }

        if (empty($error)) {
            $stmt = $conn->prepare(
                "UPDATE news SET title = ?, body = ?, image = ? WHERE id = ?"
            );
            $stmt->bind_param('sssi', $title, $body, $imageName, $id);

            if ($stmt->execute()) {
                $success         = 'Article updated successfully.';
                $article['title'] = $title;
                $article['body']  = $body;
                $article['image'] = $imageName;
            } else {
                $error = 'Update failed. Please try again.';
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
            <a href="/filmbase/admin/index.php">       <span class="nav-icon">&#128202;</span> Dashboard</a>
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
                <h1 class="admin-title">Edit Article</h1>
                <p style="color:var(--text-muted); font-size:0.9rem; margin:0;">
                    Editing: <span class="text-accent"><?php echo e(mb_substr($article['title'], 0, 60)); ?></span>
                </p>
            </div>
            <div style="display:flex; gap:0.6rem;">
                <a href="/filmbase/news-single.php?id=<?php echo $id; ?>" class="btn btn-outline btn-sm" target="_blank">
                    View Article &rarr;
                </a>
                <a href="/filmbase/admin/news-delete.php?id=<?php echo $id; ?>"
                   class="btn btn-danger btn-sm"
                   data-confirm="Delete this article permanently?">
                    Delete
                </a>
            </div>
        </div>

        <?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?></div><?php endif; ?>
        <?php if ($error):   ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>

        <div class="form-card" style="max-width:100%;">
            <form method="POST"
                  action="/filmbase/admin/news-edit.php?id=<?php echo $id; ?>"
                  enctype="multipart/form-data">

                <input type="hidden" name="article_id" value="<?php echo $id; ?>">

                <div class="form-group">
                    <label for="title">Article Title <span style="color:var(--accent)">*</span></label>
                    <input type="text" id="title" name="title"
                           value="<?php echo e($article['title']); ?>"
                           required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="body">Article Body <span style="color:var(--accent)">*</span></label>
                    <textarea id="body" name="body" rows="16" required><?php echo e($article['body']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Featured Image</label>

                    <?php if (!empty($article['image'])): ?>
                        <div class="current-image-wrap">
                            <img
                                src="/filmbase/assets/uploads/<?php echo e($article['image']); ?>"
                                alt="Current image"
                                id="img-preview"
                                onerror="this.style.display='none';">
                            <div class="current-image-info">
                                <p style="color:var(--text-muted); font-size:0.85rem; margin:0 0 0.5rem;">
                                    Current: <code style="color:var(--accent);"><?php echo e($article['image']); ?></code>
                                </p>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="remove_image" name="remove_image" value="1">
                                    <label for="remove_image" style="color:var(--danger); font-size:0.875rem;">
                                        Remove current image
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <img id="img-preview" src="" alt="" style="display:none; max-width:320px; max-height:200px; object-fit:cover; border-radius:var(--radius); border:1px solid var(--border); margin-bottom:0.75rem;">
                    <?php endif; ?>

                    <label for="image" style="margin-top:0.75rem; display:block; font-size:0.85rem; color:var(--text-muted);">
                        Upload new image to replace (JPG, PNG, WebP, GIF — max 3 MB):
                    </label>
                    <input type="file" id="image" name="image"
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           data-preview="img-preview">
                </div>

                <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.5rem;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="/filmbase/admin/index.php" class="btn btn-outline">Cancel</a>
                </div>

            </form>
        </div>

    </div>
</div>

<style>
.current-image-wrap {
    display: flex;
    gap: 1.25rem;
    align-items: flex-start;
    margin-bottom: 1rem;
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem;
}
.current-image-wrap img {
    width: 140px;
    height: 90px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid var(--border);
    flex-shrink: 0;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
$pageTitle = 'Add Gallery Image';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = '';
$error   = '';
$title   = '';

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$maxSize      = 5 * 1024 * 1024;
$uploadDir    = __DIR__ . '/../assets/uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');

    if (empty($title)) {
        $error = 'Image caption / title is required.';
    } elseif (empty($_FILES['image']['name'])) {
        $error = 'Please select an image to upload.';
    } else {
        $file     = $_FILES['image'];
        $fileType = mime_content_type($file['tmp_name']);
        $fileSize = $file['size'];
        $fileExt  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Upload error (code ' . $file['error'] . '). Please try again.';
        } elseif (!in_array($fileType, $allowedTypes)) {
            $error = 'Only JPG, PNG, WebP, and GIF images are allowed.';
        } elseif ($fileSize > $maxSize) {
            $error = 'Image must be smaller than 5 MB.';
        } else {
            $imageName = uniqid('gallery_', true) . '.' . $fileExt;

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $imageName)) {
                $stmt = $conn->prepare("INSERT INTO gallery (title, image) VALUES (?, ?)");
                $stmt->bind_param('ss', $title, $imageName);

                if ($stmt->execute()) {
                    $success = 'Image uploaded and added to the gallery successfully!';
                    $title   = '';
                } else {
                    unlink($uploadDir . $imageName);
                    $error = 'Database error. Could not save the gallery entry.';
                }
            } else {
                $error = 'Could not save the image file. Check folder permissions on assets/uploads/.';
            }
        }
    }
}

$gallery = $conn->query(
    "SELECT id, title, image, created_at FROM gallery ORDER BY created_at DESC"
)->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">

    <aside class="admin-sidebar">
        <div class="admin-sidebar-header"><h3>Admin Panel</h3></div>
        <nav class="admin-nav">
            <a href="/filmbase/admin/index.php">              <span class="nav-icon">&#128202;</span> Dashboard</a>
            <a href="/filmbase/admin/users.php">              <span class="nav-icon">&#128101;</span> Users</a>
            <a href="/filmbase/admin/news-add.php">           <span class="nav-icon">&#128221;</span> Add News</a>
            <a href="/filmbase/admin/gallery-add.php" class="active"><span class="nav-icon">&#128247;</span> Add to Gallery</a>
            <a href="/filmbase/index.php" style="margin-top:1rem; border-top:1px solid var(--border); padding-top:1rem;">
                <span class="nav-icon">&#8592;</span> Back to Site
            </a>
        </nav>
    </aside>

    <div class="admin-content">

        <div class="admin-header">
            <div>
                <h1 class="admin-title">Gallery Management</h1>
                <p style="color:var(--text-muted); font-size:0.9rem; margin:0;">
                    Upload new images and manage the public gallery.
                </p>
            </div>
            <a href="/filmbase/gallery.php" class="btn btn-outline btn-sm" target="_blank">View Gallery &rarr;</a>
        </div>

        <?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?></div><?php endif; ?>
        <?php if ($error):   ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>

        <div class="form-card" style="max-width:580px; margin-bottom:2.5rem;">
            <h2 style="font-size:1.1rem; margin-bottom:1.5rem;">Upload New Image</h2>

            <form method="POST" action="/filmbase/admin/gallery-add.php" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="title">Caption / Title <span style="color:var(--accent)">*</span></label>
                    <input type="text" id="title" name="title"
                           value="<?php echo e($title); ?>"
                           placeholder="e.g. Red Carpet Event 2024"
                           required maxlength="255" autofocus>
                </div>

                <div class="form-group">
                    <label for="image">Image File <span style="color:var(--accent)">*</span></label>
                    <input type="file" id="image" name="image"
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           required data-preview="img-preview">
                    <p style="font-size:0.8rem; color:var(--text-muted); margin-top:0.35rem;">
                        JPG, PNG, WebP, or GIF — max 5 MB. Recommended: landscape 4:3 or 16:9.
                    </p>
                </div>

                <div style="margin-bottom:1.25rem; min-height:0;">
                    <img id="img-preview" src="" alt="Preview"
                         style="display:none; width:100%; max-height:260px; object-fit:cover;
                                border-radius:var(--radius); border:1px solid var(--border);">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">
                    Upload &amp; Add to Gallery
                </button>

            </form>
        </div>

        <?php if (!empty($gallery)): ?>
        <h2 style="font-size:1.1rem; margin-bottom:1rem;">
            All Gallery Images
            <span style="color:var(--text-muted); font-weight:400; font-size:0.9rem;">(<?php echo count($gallery); ?> total)</span>
        </h2>
        <div class="admin-table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Preview</th>
                        <th>Caption</th>
                        <th>Filename</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gallery as $item): ?>
                        <tr>
                            <td><?php echo (int)$item['id']; ?></td>
                            <td>
                                <?php if (!empty($item['image'])): ?>
                                    <img src="/filmbase/assets/uploads/<?php echo e($item['image']); ?>"
                                         alt="<?php echo e($item['title']); ?>"
                                         style="width:72px; height:50px; object-fit:cover; border-radius:4px; border:1px solid var(--border);"
                                         onerror="this.style.display='none';">
                                <?php else: ?>
                                    <span style="color:var(--text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($item['title']); ?></td>
                            <td>
                                <code style="font-size:0.78rem; color:var(--text-muted);">
                                    <?php echo e($item['image'] ?: '—'); ?>
                                </code>
                            </td>
                            <td><?php echo e(date('d M Y', strtotime($item['created_at']))); ?></td>
                            <td>
                                <a href="/filmbase/admin/gallery-add.php?delete=<?php echo (int)$item['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   data-confirm="Delete &quot;<?php echo e($item['title']); ?>&quot; from the gallery? The image file will also be removed.">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $fetch = $conn->prepare("SELECT image FROM gallery WHERE id = ? LIMIT 1");
    $fetch->bind_param('i', $delId);
    $fetch->execute();
    $row = $fetch->get_result()->fetch_assoc();

    if ($row) {
        if (!empty($row['image'])) {
            $path = __DIR__ . '/../assets/uploads/' . $row['image'];
            if (file_exists($path)) unlink($path);
        }
        $del = $conn->prepare("DELETE FROM gallery WHERE id = ?");
        $del->bind_param('i', $delId);
        $del->execute();
    }
    header('Location: /filmbase/admin/gallery-add.php?removed=1');
    exit;
}
?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

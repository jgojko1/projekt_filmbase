<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: /filmbase/admin/index.php');
    exit;
}

$stmt = $conn->prepare("SELECT id, image FROM news WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();

if (!$article) {
    header('Location: /filmbase/admin/index.php?error=notfound');
    exit;
}

if (!empty($article['image'])) {
    $path = __DIR__ . '/../assets/uploads/' . $article['image'];
    if (file_exists($path)) {
        unlink($path);
    }
}

$del = $conn->prepare("DELETE FROM news WHERE id = ?");
$del->bind_param('i', $id);
$del->execute();

header('Location: /filmbase/admin/index.php?tab=news&deleted=1');
exit;

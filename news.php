<?php
$pageTitle = 'News';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$perPage     = 6;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($currentPage - 1) * $perPage;

$totalResult = $conn->query("SELECT COUNT(*) AS cnt FROM news");
$totalRows   = $totalResult ? (int)$totalResult->fetch_assoc()['cnt'] : 0;
$totalPages  = (int)ceil($totalRows / $perPage);

$stmt = $conn->prepare("SELECT id, title, body, image, created_at FROM news ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();
$news = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <div class="container">
        <p class="breadcrumb"><a href="/filmbase/index.php">Home</a> &rsaquo; News</p>
        <h1>Film News</h1>
        <p>The latest stories from the world of cinema</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <?php if (empty($news)): ?>
            <div class="alert alert-info">No news articles have been published yet. Check back soon!</div>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($news as $article): ?>
                    <article class="card">
                        <div class="card-img-wrap">
                            <?php if (!empty($article['image'])): ?>
                                <img
                                    src="/filmbase/assets/uploads/<?php echo e($article['image']); ?>"
                                    alt="<?php echo e($article['title']); ?>"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div class="card-img-placeholder" style="display:none;">&#127909;</div>
                            <?php else: ?>
                                <div class="card-img-placeholder">&#127909;</div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <p class="card-meta">
                                &#128197; <?php echo e(date('d M Y', strtotime($article['created_at']))); ?>
                            </p>
                            <h2 class="card-title">
                                <a href="/filmbase/news-single.php?id=<?php echo (int)$article['id']; ?>">
                                    <?php echo e($article['title']); ?>
                                </a>
                            </h2>
                            <p class="card-excerpt">
                                <?php echo e(mb_substr(strip_tags($article['body']), 0, 160)) . '...'; ?>
                            </p>
                            <div class="card-footer">
                                <a href="/filmbase/news-single.php?id=<?php echo (int)$article['id']; ?>" class="btn btn-outline btn-sm">
                                    Read More &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="pagination" aria-label="News pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?php echo $currentPage - 1; ?>" class="btn btn-outline btn-sm">&laquo; Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>"
                           class="btn btn-sm <?php echo $i === $currentPage ? 'btn-primary' : 'btn-outline'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?php echo $currentPage + 1; ?>" class="btn btn-outline btn-sm">Next &raquo;</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<style>
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-top: 3rem;
    flex-wrap: wrap;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

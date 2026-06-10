<?php
$pageTitle = 'Gallery';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$result  = $conn->query("SELECT id, title, image, created_at FROM gallery ORDER BY created_at DESC");
$gallery = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <div class="container">
        <p class="breadcrumb"><a href="/filmbase/index.php">Home</a> &rsaquo; Gallery</p>
        <h1>Photo Gallery</h1>
        <p>Behind the scenes, premieres, and iconic moments from the world of cinema</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <?php if (empty($gallery)): ?>
            <div class="alert alert-info">No gallery images have been uploaded yet. Check back soon!</div>
        <?php else: ?>
            <p class="gallery-count text-muted" style="margin-bottom:1.5rem;">
                Showing <?php echo count($gallery); ?> image<?php echo count($gallery) !== 1 ? 's' : ''; ?>
            </p>

            <div class="gallery-grid">
                <?php foreach ($gallery as $item): ?>
                    <figure class="gallery-item" tabindex="0" data-caption="<?php echo e($item['title']); ?>">
                        <?php if (!empty($item['image'])): ?>
                            <img
                                src="/filmbase/assets/uploads/<?php echo e($item['image']); ?>"
                                alt="<?php echo e($item['title']); ?>"
                                loading="lazy"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            >
                            <div class="gallery-placeholder" style="display:none;">&#127909;</div>
                        <?php else: ?>
                            <div class="gallery-placeholder">&#127909;</div>
                        <?php endif; ?>
                        <figcaption class="gallery-caption"><?php echo e($item['title']); ?></figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Image viewer" style="display:none;">
    <button class="lightbox-close" id="lightbox-close" aria-label="Close">&times;</button>
    <button class="lightbox-prev" id="lightbox-prev" aria-label="Previous">&#8249;</button>
    <div class="lightbox-content">
        <img src="" alt="" id="lightbox-img">
        <p id="lightbox-caption"></p>
    </div>
    <button class="lightbox-next" id="lightbox-next" aria-label="Next">&#8250;</button>
</div>

<style>
.lightbox {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.92);
    z-index: 2000;
    display: flex !important;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    padding: 1rem;
}
.lightbox[style*="display:none"] { display: none !important; }
.lightbox-content {
    max-width: 900px;
    width: 100%;
    text-align: center;
}
.lightbox-content img {
    max-height: 80vh;
    max-width: 100%;
    border-radius: 8px;
    border: 2px solid var(--border);
    object-fit: contain;
    margin: 0 auto;
}
.lightbox-content p {
    color: var(--text-muted);
    margin-top: 1rem;
    font-size: 0.95rem;
}
.lightbox-close {
    position: absolute;
    top: 1rem; right: 1.5rem;
    background: none; border: none;
    color: #fff; font-size: 2.5rem;
    cursor: pointer; line-height: 1;
    transition: color 0.2s;
}
.lightbox-close:hover { color: var(--accent); }
.lightbox-prev,
.lightbox-next {
    background: rgba(255,255,255,0.08);
    border: 1px solid var(--border);
    color: #fff;
    font-size: 2rem;
    width: 48px; height: 48px;
    border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background 0.2s, color 0.2s;
}
.lightbox-prev:hover,
.lightbox-next:hover { background: var(--accent); color: #000; }
</style>

<script>
(function () {
    const items    = document.querySelectorAll('.gallery-item');
    const lightbox = document.getElementById('lightbox');
    const lbImg    = document.getElementById('lightbox-img');
    const lbCap    = document.getElementById('lightbox-caption');
    const btnClose = document.getElementById('lightbox-close');
    const btnPrev  = document.getElementById('lightbox-prev');
    const btnNext  = document.getElementById('lightbox-next');
    let current = 0;

    function getImgSrc(item) {
        const img = item.querySelector('img');
        return img ? img.src : '';
    }
    function getCaption(item) {
        return item.getAttribute('data-caption') || '';
    }

    function open(index) {
        current = index;
        const src = getImgSrc(items[current]);
        lbImg.src = src;
        lbImg.alt = getCaption(items[current]);
        lbCap.textContent = getCaption(items[current]);
        lightbox.style.display = '';
        document.body.style.overflow = 'hidden';
    }

    function close() {
        lightbox.style.display = 'none';
        document.body.style.overflow = '';
    }

    items.forEach(function (item, i) {
        item.addEventListener('click',   function () { open(i); });
        item.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') open(i); });
    });

    btnClose.addEventListener('click', close);
    btnPrev.addEventListener('click',  function () { open((current - 1 + items.length) % items.length); });
    btnNext.addEventListener('click',  function () { open((current + 1) % items.length); });

    lightbox.addEventListener('click', function (e) { if (e.target === lightbox) close(); });

    document.addEventListener('keydown', function (e) {
        if (lightbox.style.display === 'none') return;
        if (e.key === 'Escape')     close();
        if (e.key === 'ArrowLeft')  open((current - 1 + items.length) % items.length);
        if (e.key === 'ArrowRight') open((current + 1) % items.length);
    });
}());
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

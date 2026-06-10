<?php
$pageTitle = 'Home';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$news = [];
$result = $conn->query("SELECT id, title, body, image, created_at FROM news ORDER BY created_at DESC LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <p class="hero-eyebrow">&#127909; Welcome to FilmBase</p>
            <h1>Your Ultimate <span class="text-accent">Cinema</span> Database</h1>
            <p>Discover the latest film news, explore our curated gallery, search millions of movies, and stay up to date with the world of cinema — all in one place.</p>
            <div class="hero-actions">
                <a href="/filmbase/news.php"     class="btn btn-primary">Latest News</a>
                <a href="/filmbase/api-omdb.php" class="btn btn-outline">Search Movies</a>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="home-intro-grid">
            <div class="home-intro-text">
                <h2 class="section-title">About FilmBase</h2>
                <p class="section-subtitle">Cinema knowledge, curated for enthusiasts</p>

                <p>FilmBase is a comprehensive online platform dedicated to the art and culture of cinema. Founded by a team of passionate film enthusiasts, our mission is to bring together news, reviews, and resources for movie lovers of every taste and background. Whether you are a casual viewer or a dedicated cinephile, FilmBase has something for you.</p>

                <p>Our editorial team works around the clock to deliver accurate, insightful, and engaging content covering everything from major Hollywood blockbusters to hidden gems from independent and international cinema. We believe that great storytelling transcends borders and genres, and our coverage reflects that belief through a wide-ranging selection of articles, interviews, and features.</p>

                <p>Beyond news and reviews, FilmBase offers powerful tools that put movie information at your fingertips. Search our integrated OMDB movie database to find detailed information on virtually any film ever made. Browse our curated gallery of behind-the-scenes moments, premieres, and iconic film stills. Join our growing community by registering for a free account and become part of the conversation shaping cinema culture today.</p>

                <a href="/filmbase/about.php" class="btn btn-outline" style="margin-top:0.5rem">Learn More About Us</a>
            </div>

            <div class="home-intro-media">
                <figure>
                    <img
                        src="/filmbase/assets/images/cinema-hero.jpg"
                        alt="A grand cinema auditorium with rows of red velvet seats illuminated by the glow of the screen"
                        style="border-radius:8px; border:1px solid var(--border); width:100%;"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div class="card-img-placeholder" style="display:none; aspect-ratio:4/3; background:var(--bg-card); border:1px solid var(--border); border-radius:8px;">&#127909;</div>
                    <figcaption>The magic of cinema — where stories come to life on the big screen.</figcaption>
                </figure>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--bg-secondary); border-top:1px solid var(--border); border-bottom:1px solid var(--border);">
    <div class="container">
        <h2 class="section-title text-center" style="display:block;">What We Offer</h2>
        <p class="section-subtitle text-center">Everything a film lover needs</p>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">&#128240;</div>
                <h3>Film News</h3>
                <p>Stay informed with daily updates covering box office results, casting announcements, festival coverage, and in-depth industry analysis.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128247;</div>
                <h3>Gallery</h3>
                <p>Browse our handpicked collection of film stills, behind-the-scenes photography, and exclusive event coverage from around the world.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128269;</div>
                <h3>Movie Search</h3>
                <p>Search millions of titles using the OMDB API. Get instant access to ratings, plot summaries, cast details, and official posters.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#127758;</div>
                <h3>Exchange Rates</h3>
                <p>Follow live EUR/USD exchange rates from the Croatian National Bank — useful for tracking global box office earnings in local currency.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Latest News</h2>
        <p class="section-subtitle">Fresh from the world of cinema</p>

        <?php if (empty($news)): ?>
            <div class="alert alert-info">No news articles yet. Check back soon!</div>
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
                            <h3 class="card-title">
                                <a href="/filmbase/news-single.php?id=<?php echo (int)$article['id']; ?>">
                                    <?php echo e($article['title']); ?>
                                </a>
                            </h3>
                            <p class="card-excerpt">
                                <?php echo e(mb_substr(strip_tags($article['body']), 0, 140)) . '...'; ?>
                            </p>
                            <div class="card-footer">
                                <a href="/filmbase/news-single.php?id=<?php echo (int)$article['id']; ?>" class="btn btn-outline btn-sm">Read More</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="text-center" style="margin-top:2rem;">
                <a href="/filmbase/news.php" class="btn btn-primary">View All News</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section" style="background:var(--bg-secondary); border-top:1px solid var(--border); padding:2.5rem 0;">
    <div class="container text-center">
        <h2 style="font-size:1.4rem; margin-bottom:0.5rem;">Follow FilmBase</h2>
        <p style="color:var(--text-muted); margin-bottom:1.5rem;">Join our community on social media for daily film content</p>
        <div class="social-icons" style="justify-content:center; gap:1rem;">
            <a href="#" class="social-icon" aria-label="Facebook"  target="_blank" rel="noopener">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a href="#" class="social-icon" aria-label="Instagram" target="_blank" rel="noopener">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
            </a>
            <a href="#" class="social-icon" aria-label="YouTube"   target="_blank" rel="noopener">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.96-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#111827"/></svg>
            </a>
            <a href="#" class="social-icon" aria-label="Twitter/X" target="_blank" rel="noopener">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h4l12 16h-4z"/><path d="M4 20 20 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/></svg>
            </a>
            <a href="#" class="social-icon" aria-label="LinkedIn"  target="_blank" rel="noopener">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
            </a>
        </div>
    </div>
</section>

<style>
.home-intro-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: start;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-top: 2rem;
}
.feature-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.75rem 1.25rem;
    text-align: center;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.feature-card:hover { transform: translateY(-4px); box-shadow: 0 4px 20px rgba(0,0,0,0.5); }
.feature-icon { font-size: 2.2rem; margin-bottom: 1rem; }
.feature-card h3 { font-size: 1.05rem; margin-bottom: 0.6rem; }
.feature-card p  { color: var(--text-muted); font-size: 0.9rem; margin: 0; }

@media (max-width: 1024px) { .features-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px)  { .home-intro-grid { grid-template-columns: 1fr; } .features-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px)  { .features-grid { grid-template-columns: 1fr; } }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

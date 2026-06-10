<?php
$pageTitle = 'About';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <div class="container">
        <p class="breadcrumb"><a href="/filmbase/index.php">Home</a> &rsaquo; About</p>
        <h1>About FilmBase</h1>
        <p>Our story, our mission, and the team behind the platform</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <div class="about-grid">
            <div class="about-text">
                <h2 class="section-title">Who We Are</h2>
                <h3 class="about-subheading">Passionate storytellers dedicated to the art of cinema</h3>

                <p>FilmBase was born out of a simple but powerful idea: that cinema deserves a platform as rich, nuanced, and dedicated as the art form itself. Founded in 2020 by a group of film students and industry professionals, FilmBase has grown from a small blog into one of the most comprehensive cinema databases and news outlets available online. Our team spans multiple continents and brings together critics, journalists, photographers, and technologists united by a shared love of film.</p>

                <p>We believe that movies are more than entertainment — they are windows into other worlds, reflections of our society, and vessels for empathy and understanding. Whether a film is a Hollywood blockbuster seen by millions or an independent short screened only at a local festival, every story matters. FilmBase is committed to covering the full spectrum of cinema, from mainstream releases to the hidden gems that deserve a wider audience.</p>

                <p>Our editorial standards are uncompromising. Every article, review, and feature published on FilmBase goes through a rigorous editorial process to ensure accuracy, fairness, and depth. We do not accept paid reviews or promotional content disguised as editorial, and we are proud to maintain full independence from the studios and distributors whose work we cover. Our readers trust us because we have always put journalism first.</p>

                <p>Beyond words, FilmBase invests heavily in visual storytelling. Our gallery showcases exclusive photography from film sets, award ceremonies, and festivals around the globe. Our technical tools — including the integrated OMDB movie search and live exchange rate tracker — make FilmBase a one-stop destination for anyone who takes cinema seriously. We are constantly developing new features and expanding our coverage to serve our growing global community.</p>
            </div>

            <div class="about-media">
                <div class="about-video-wrap">
                    <iframe
                        src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                        title="FilmBase — The Art of Cinema"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                </div>
                <p style="color:var(--text-muted); font-size:0.85rem; margin-top:0.75rem; text-align:center;">
                    &#9654; FilmBase: The Art of Cinema — our story in two minutes
                </p>

                <div class="about-stats">
                    <div class="about-stat">
                        <span class="about-stat-number">500+</span>
                        <span class="about-stat-label">Articles Published</span>
                    </div>
                    <div class="about-stat">
                        <span class="about-stat-number">12K+</span>
                        <span class="about-stat-label">Registered Users</span>
                    </div>
                    <div class="about-stat">
                        <span class="about-stat-number">80+</span>
                        <span class="about-stat-label">Countries Reached</span>
                    </div>
                    <div class="about-stat">
                        <span class="about-stat-number">4</span>
                        <span class="about-stat-label">Years Online</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<section class="section" style="background:var(--bg-secondary); border-top:1px solid var(--border); border-bottom:1px solid var(--border);">
    <div class="container">
        <h2 class="section-title text-center" style="display:block;">Our Values</h2>
        <p class="section-subtitle text-center">The principles that guide everything we do</p>

        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">&#9878;</div>
                <h3>Independence</h3>
                <p>We accept no paid placements or sponsored reviews. Our opinions are our own, shaped only by the films we watch and the standards we hold.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">&#127759;</div>
                <h3>Diversity</h3>
                <p>Cinema is a global art form. We actively seek out films and voices from underrepresented regions, communities, and perspectives.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">&#128218;</div>
                <h3>Depth</h3>
                <p>We go beyond surface-level coverage. Our features and analyses explore the cultural, historical, and artistic dimensions of the films we discuss.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">&#128101;</div>
                <h3>Community</h3>
                <p>FilmBase is built by and for film lovers. We listen to our readers, welcome debate, and celebrate the shared joy of watching great films.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Meet the Team</h2>
        <p class="section-subtitle">The people who make FilmBase possible</p>

        <div class="team-grid">
            <div class="team-card">
                <div class="team-avatar">&#128104;</div>
                <h4>Ivan Horvat</h4>
                <p class="team-role">Editor in Chief</p>
                <p class="team-bio">With fifteen years of film journalism experience, Ivan sets the editorial direction and tone of FilmBase.</p>
            </div>
            <div class="team-card">
                <div class="team-avatar">&#128105;</div>
                <h4>Ana Novak</h4>
                <p class="team-role">Senior Film Critic</p>
                <p class="team-bio">Ana covers European cinema and festival circuit with a focus on auteur filmmaking and documentary.</p>
            </div>
            <div class="team-card">
                <div class="team-avatar">&#128104;</div>
                <h4>Marco Rossi</h4>
                <p class="team-role">Technology Lead</p>
                <p class="team-bio">Marco built and maintains the FilmBase platform, keeping the experience fast, accessible, and reliable.</p>
            </div>
            <div class="team-card">
                <div class="team-avatar">&#128105;</div>
                <h4>Sara Kovač</h4>
                <p class="team-role">Photo Editor</p>
                <p class="team-bio">Sara curates the gallery and manages our network of photographers across major film festivals.</p>
            </div>
        </div>
    </div>
</section>

<style>
.about-subheading {
    font-size: 1.1rem;
    color: var(--accent);
    font-weight: 600;
    margin-bottom: 1.5rem;
}
.about-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-top: 1.5rem;
}
.about-stat {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem;
    text-align: center;
}
.about-stat-number { display: block; font-size: 1.8rem; font-weight: 800; color: var(--accent); }
.about-stat-label  { display: block; font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; }

.values-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-top: 2rem;
}
.value-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.75rem 1.25rem;
    text-align: center;
}
.value-icon { font-size: 2rem; margin-bottom: 0.75rem; }
.value-card h3 { font-size: 1rem; margin-bottom: 0.5rem; }
.value-card p  { color: var(--text-muted); font-size: 0.9rem; margin: 0; }

.team-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-top: 2rem;
}
.team-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2rem 1.25rem;
    text-align: center;
}
.team-avatar { font-size: 3rem; margin-bottom: 0.75rem; }
.team-card h4     { font-size: 1.05rem; margin-bottom: 0.25rem; }
.team-role        { color: var(--accent); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.75rem; }
.team-bio         { color: var(--text-muted); font-size: 0.875rem; margin: 0; }

@media (max-width: 1024px) {
    .values-grid { grid-template-columns: repeat(2, 1fr); }
    .team-grid   { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .values-grid { grid-template-columns: 1fr; }
    .team-grid   { grid-template-columns: 1fr 1fr; }
    .about-stats { grid-template-columns: 1fr 1fr; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

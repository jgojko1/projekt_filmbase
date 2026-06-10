<?php
$pageTitle = 'Movie Search';
require_once __DIR__ . '/includes/auth.php';

$movie  = null;
$error  = '';
$query  = trim($_GET['q'] ?? '');
$year   = trim($_GET['year'] ?? '');

define('OMDB_API_KEY', 'YOUR_OMDB_API_KEY');

if ($query !== '') {
    $params = http_build_query([
        'apikey' => OMDB_API_KEY,
        't'      => $query,
        'y'      => $year,
        'plot'   => 'full',
        'r'      => 'json',
    ]);

    $url = 'https://www.omdbapi.com/?' . $params;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_USERAGENT      => 'FilmBase/1.0',
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        $error = 'Could not connect to OMDB API: ' . htmlspecialchars($curlErr);
    } elseif ($response) {
        $data = json_decode($response, true);
        if (isset($data['Response']) && $data['Response'] === 'True') {
            $movie = $data;
        } else {
            $error = $data['Error'] ?? 'Movie not found. Try a different title.';
        }
    } else {
        $error = 'No response from OMDB API. Please try again.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <div class="container">
        <p class="breadcrumb"><a href="/filmbase/index.php">Home</a> &rsaquo; Movie Search</p>
        <h1>Movie Search</h1>
        <p>Search millions of titles powered by the OMDB API</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <div class="api-search-box">
            <h2 style="font-size:1.2rem; margin-bottom:1.25rem;">Search for a Movie</h2>
            <form method="GET" action="/filmbase/api-omdb.php">
                <div class="form-group">
                    <label for="q">Movie Title</label>
                    <input type="text" id="q" name="q"
                           value="<?php echo e($query); ?>"
                           placeholder="e.g. The Godfather, Inception, Dune..."
                           required maxlength="200">
                </div>
                <div class="form-group" style="max-width:140px;">
                    <label for="year">Year (optional)</label>
                    <input type="number" id="year" name="year"
                           value="<?php echo e($year); ?>"
                           placeholder="2024" min="1888" max="2030">
                </div>
                <div style="align-self:flex-end;">
                    <button type="submit" class="btn btn-primary">Search &rarr;</button>
                </div>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if ($movie): ?>
            <div class="movie-result">

                <div>
                    <?php if (!empty($movie['Poster']) && $movie['Poster'] !== 'N/A'): ?>
                        <img
                            class="movie-poster"
                            src="<?php echo e($movie['Poster']); ?>"
                            alt="<?php echo e($movie['Title']); ?> poster"
                            style="width:100%;"
                        >
                    <?php else: ?>
                        <div class="movie-poster-placeholder">&#127909;</div>
                    <?php endif; ?>
                </div>

                <div class="movie-details">
                    <h2 class="movie-title"><?php echo e($movie['Title']); ?></h2>

                    <?php if (!empty($movie['imdbRating']) && $movie['imdbRating'] !== 'N/A'): ?>
                        <p class="movie-rating">
                            &#11088; <?php echo e($movie['imdbRating']); ?> / 10
                            <span style="font-size:0.85rem; color:var(--text-muted); font-weight:400;">
                                &nbsp;(<?php echo !empty($movie['imdbVotes']) && $movie['imdbVotes'] !== 'N/A' ? e($movie['imdbVotes']) . ' votes' : 'IMDB'; ?>)
                            </span>
                        </p>
                    <?php endif; ?>

                    <div class="movie-meta-list">
                        <?php
                        $metaFields = [
                            'Year'     => '&#128197; Year',
                            'Rated'    => '&#127384; Rated',
                            'Runtime'  => '&#9201; Runtime',
                            'Genre'    => '&#127916; Genre',
                            'Director' => '&#127910; Director',
                            'Writer'   => '&#9997; Writer',
                            'Actors'   => '&#127775; Cast',
                            'Language' => '&#127760; Language',
                            'Country'  => '&#127956; Country',
                            'Awards'   => '&#127942; Awards',
                            'Released' => '&#128198; Released',
                            'BoxOffice'=> '&#128181; Box Office',
                        ];
                        foreach ($metaFields as $key => $label):
                            if (!empty($movie[$key]) && $movie[$key] !== 'N/A'):
                        ?>
                            <span><?php echo $label; ?>: <strong><?php echo e($movie[$key]); ?></strong></span>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>

                    <?php if (!empty($movie['Plot']) && $movie['Plot'] !== 'N/A'): ?>
                        <p class="movie-plot"><?php echo e($movie['Plot']); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($movie['Ratings']) && count($movie['Ratings']) > 0): ?>
                        <div class="ratings-row">
                            <?php foreach ($movie['Ratings'] as $rating): ?>
                                <div class="rating-badge">
                                    <span class="rating-source"><?php echo e($rating['Source']); ?></span>
                                    <span class="rating-value"><?php echo e($rating['Value']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div style="margin-top:1.5rem;">
                        <?php if (!empty($movie['imdbID']) && $movie['imdbID'] !== 'N/A'): ?>
                            <a href="https://www.imdb.com/title/<?php echo e($movie['imdbID']); ?>/"
                               class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                                View on IMDB &rarr;
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        <?php elseif ($query === ''): ?>
            <div class="omdb-empty">
                <div class="omdb-empty-icon">&#127909;</div>
                <h3>Search for any movie</h3>
                <p>Enter a title above to get details including ratings, cast, plot, and more — powered by the Open Movie Database.</p>
                <div class="omdb-suggestions">
                    <p style="color:var(--text-muted); font-size:0.9rem; margin-bottom:0.75rem;">Popular searches:</p>
                    <div class="suggestion-chips">
                        <a href="?q=The+Godfather"    class="chip">The Godfather</a>
                        <a href="?q=Inception"         class="chip">Inception</a>
                        <a href="?q=Dune"              class="chip">Dune</a>
                        <a href="?q=Interstellar"      class="chip">Interstellar</a>
                        <a href="?q=Parasite"          class="chip">Parasite</a>
                        <a href="?q=The+Dark+Knight"   class="chip">The Dark Knight</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<section style="background:var(--bg-secondary); border-top:1px solid var(--border); padding:1.5rem 0;">
    <div class="container">
        <p style="color:var(--text-muted); font-size:0.85rem; text-align:center; margin:0;">
            Movie data provided by <a href="https://www.omdbapi.com/" target="_blank" rel="noopener">OMDB API</a>.
            Get your free API key at <a href="https://www.omdbapi.com/apikey.aspx" target="_blank" rel="noopener">omdbapi.com/apikey.aspx</a>
            and replace <code style="color:var(--accent);">YOUR_OMDB_API_KEY</code> in <code>api-omdb.php</code>.
        </p>
    </div>
</section>

<style>
.omdb-empty {
    text-align: center;
    padding: 4rem 1rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
}
.omdb-empty-icon { font-size: 4rem; margin-bottom: 1rem; }
.omdb-empty h3   { font-size: 1.4rem; margin-bottom: 0.75rem; }
.omdb-empty p    { color: var(--text-muted); max-width: 480px; margin: 0 auto 1.5rem; }
.omdb-suggestions { margin-top: 1.5rem; }
.suggestion-chips { display: flex; flex-wrap: wrap; gap: 0.6rem; justify-content: center; }
.chip {
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    color: var(--text-muted);
    padding: 0.4rem 1rem;
    border-radius: 50px;
    font-size: 0.875rem;
    transition: border-color 0.2s, color 0.2s;
}
.chip:hover { border-color: var(--accent); color: var(--accent); }

.ratings-row {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 1.25rem;
}
.rating-badge {
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.5rem 0.9rem;
    text-align: center;
}
.rating-source { display: block; font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.2rem; }
.rating-value  { display: block; font-size: 1rem; font-weight: 700; color: var(--accent); }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

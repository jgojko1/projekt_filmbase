<?php
$pageTitle = 'Exchange Rates';
require_once __DIR__ . '/includes/auth.php';

$rates    = [];
$error    = '';
$lastFetch = null;

$apiUrl = 'https://api.hnb.hr/tecajn-eur/v3';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_USERAGENT      => 'FilmBase/1.0',
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    $error = 'Could not connect to the HNB API: ' . htmlspecialchars($curlErr);
} elseif ($httpCode !== 200) {
    $error = 'HNB API returned HTTP ' . $httpCode . '. Please try again later.';
} elseif ($response) {
    $data = json_decode($response, true);
    if (is_array($data) && count($data) > 0) {
        $rates     = $data;
        $lastFetch = date('d.m.Y H:i:s');
    } else {
        $error = 'No exchange rate data returned from HNB API.';
    }
} else {
    $error = 'Empty response from HNB API. Please try again later.';
}

function cleanRate(string $val): string {
    return str_replace(',', '.', trim($val));
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <div class="container">
        <p class="breadcrumb"><a href="/filmbase/index.php">Home</a> &rsaquo; Exchange Rates</p>
        <h1>Exchange Rates</h1>
        <p>Live currency data from the Croatian National Bank (HNB)</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <div class="hnb-info-bar">
            <div class="hnb-info-item">
                <span class="hnb-info-label">Data Source</span>
                <span class="hnb-info-value">
                    <a href="https://api.hnb.hr" target="_blank" rel="noopener">Croatian National Bank (HNB)</a>
                </span>
            </div>
            <div class="hnb-info-item">
                <span class="hnb-info-label">Base Currency</span>
                <span class="hnb-info-value">EUR (Euro)</span>
            </div>
            <div class="hnb-info-item">
                <span class="hnb-info-label">Endpoint</span>
                <span class="hnb-info-value"><code>api.hnb.hr/tecajn-eur/v3</code></span>
            </div>
            <?php if ($lastFetch): ?>
            <div class="hnb-info-item">
                <span class="hnb-info-label">Fetched At</span>
                <span class="hnb-info-value"><?php echo e($lastFetch); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($rates)): ?>

            <?php
            $highlights = ['USD', 'GBP', 'CHF', 'JPY'];
            $highlighted = [];
            foreach ($rates as $rate) {
                $code = strtoupper($rate['valuta'] ?? '');
                if (in_array($code, $highlights)) {
                    $highlighted[$code] = $rate;
                }
            }
            ?>

            <?php if (!empty($highlighted)): ?>
            <div class="hnb-highlights">
                <?php foreach ($highlights as $code):
                    if (!isset($highlighted[$code])) continue;
                    $r = $highlighted[$code];
                    $srednji = cleanRate($r['srednji_tecaj'] ?? '0');
                ?>
                <div class="hnb-highlight-card">
                    <div class="hnb-flag"><?php echo currencyFlag($code); ?></div>
                    <div class="hnb-highlight-body">
                        <span class="hnb-highlight-code">EUR / <?php echo e($code); ?></span>
                        <span class="hnb-highlight-rate"><?php echo e(number_format((float)$srednji, 4, '.', ',')); ?></span>
                        <span class="hnb-highlight-label"><?php echo e($r['naziv_valute'] ?? $code); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h2 style="font-size:1.2rem; margin:2rem 0 1rem;">All Exchange Rates</h2>

            <div class="hnb-table-wrap">
                <table class="hnb-table">
                    <thead>
                        <tr>
                            <th>Currency Code</th>
                            <th>Currency Name</th>
                            <th>Unit</th>
                            <th>Buying Rate</th>
                            <th>Middle Rate</th>
                            <th>Selling Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rates as $rate):
                            $code    = strtoupper($rate['valuta']        ?? '');
                            $name    = $rate['naziv_valute']              ?? '';
                            $unit    = $rate['jedinica']                  ?? '1';
                            $buying  = cleanRate($rate['kupovni_tecaj']   ?? '');
                            $middle  = cleanRate($rate['srednji_tecaj']   ?? '');
                            $selling = cleanRate($rate['prodajni_tecaj']  ?? '');
                        ?>
                        <tr>
                            <td>
                                <span class="currency-code">
                                    <?php echo currencyFlag($code); ?>
                                    <?php echo e($code); ?>
                                </span>
                            </td>
                            <td><?php echo e($name); ?></td>
                            <td style="text-align:center;"><?php echo e($unit); ?></td>
                            <td class="rate-cell"><?php echo e($buying  !== '' ? number_format((float)$buying,  4, '.', ',') : '—'); ?></td>
                            <td class="rate-cell rate-middle"><?php echo e($middle  !== '' ? number_format((float)$middle,  4, '.', ',') : '—'); ?></td>
                            <td class="rate-cell"><?php echo e($selling !== '' ? number_format((float)$selling, 4, '.', ',') : '—'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p style="color:var(--text-muted); font-size:0.82rem; margin-top:1rem;">
                * All rates are expressed as the amount of HRK (Croatian Kuna equivalent) per unit of foreign currency, relative to EUR.
                Data is published daily by the Croatian National Bank.
            </p>

        <?php endif; ?>

    </div>
</section>

<section style="background:var(--bg-secondary); border-top:1px solid var(--border); padding:1.5rem 0;">
    <div class="container">
        <p style="color:var(--text-muted); font-size:0.85rem; text-align:center; margin:0;">
            Exchange rate data provided by the
            <a href="https://api.hnb.hr" target="_blank" rel="noopener">Croatian National Bank (HNB) Open API</a>.
            Rates are updated each business day.
        </p>
    </div>
</section>

<?php
function currencyFlag(string $code): string {
    $flags = [
        'USD' => '🇺🇸', 'EUR' => '🇪🇺', 'GBP' => '🇬🇧', 'JPY' => '🇯🇵',
        'CHF' => '🇨🇭', 'AUD' => '🇦🇺', 'CAD' => '🇨🇦', 'SEK' => '🇸🇪',
        'NOK' => '🇳🇴', 'DKK' => '🇩🇰', 'CZK' => '🇨🇿', 'HUF' => '🇭🇺',
        'PLN' => '🇵🇱', 'RON' => '🇷🇴', 'BGN' => '🇧🇬', 'TRY' => '🇹🇷',
        'RUB' => '🇷🇺', 'CNY' => '🇨🇳', 'HKD' => '🇭🇰', 'SGD' => '🇸🇬',
        'NZD' => '🇳🇿', 'MXN' => '🇲🇽', 'ZAR' => '🇿🇦', 'BRL' => '🇧🇷',
        'INR' => '🇮🇳', 'KRW' => '🇰🇷', 'IDR' => '🇮🇩', 'MYR' => '🇲🇾',
        'THB' => '🇹🇭', 'PHP' => '🇵🇭', 'ISK' => '🇮🇸', 'HRK' => '🇭🇷',
    ];
    return $flags[$code] ?? '💱';
}
?>

<style>
.hnb-info-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.25rem 1.5rem;
    margin-bottom: 2rem;
}
.hnb-info-item { display: flex; flex-direction: column; gap: 0.2rem; }
.hnb-info-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
.hnb-info-value { font-size: 0.9rem; color: var(--text-primary); }
.hnb-info-value code { color: var(--accent); font-size: 0.85rem; }

.hnb-highlights {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1rem;
}
.hnb-highlight-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: border-color 0.25s;
}
.hnb-highlight-card:hover { border-color: var(--accent); }
.hnb-flag { font-size: 2rem; flex-shrink: 0; }
.hnb-highlight-code  { display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem; }
.hnb-highlight-rate  { display: block; font-size: 1.5rem; font-weight: 800; color: var(--accent); line-height: 1.1; }
.hnb-highlight-label { display: block; font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem; }

.currency-code { display: flex; align-items: center; gap: 0.4rem; font-weight: 600; font-size: 0.9rem; }
.rate-cell { font-family: 'Courier New', monospace; text-align: right; }
.rate-middle { color: var(--accent); font-weight: 600; }

@media (max-width: 900px)  { .hnb-highlights { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px)  { .hnb-highlights { grid-template-columns: 1fr 1fr; } }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

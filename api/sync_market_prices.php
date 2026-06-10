<?php
/**
 * NAOS — WFP / HDX Market Price Sync
 *
 * Fetches real Malawi food price data from the World Food Programme
 * via the Humanitarian Data Exchange (HDX) public API, then:
 *   1. Normalises commodity prices to MWK per kg
 *   2. Updates crop_config.current_price
 *   3. Appends a row to market_price_history
 *   4. Logs the sync run in wfp_sync_log
 *
 * Called either:
 *   - Manually by an admin via the Market Prices admin panel, or
 *   - Automatically when market.php detects stale data (>24 h)
 */

header('Content-Type: application/json');
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

/* ── Access control ───────────────────────────────────────────────────────── */
$isAdmin    = isLoggedIn() && getUserRole() === 'admin';
$isAutoSync = ($_GET['auto'] ?? '') === '1';   // internal call from market.php

if (!$isAdmin && !$isAutoSync) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

/* ── Ensure required tables exist ────────────────────────────────────────── */
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS wfp_sync_log (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        synced_at     DATETIME NOT NULL,
        source        VARCHAR(100) DEFAULT 'WFP/HDX',
        records_found INT DEFAULT 0,
        crops_updated INT DEFAULT 0,
        status        ENUM('success','partial','failed') DEFAULT 'success',
        message       TEXT
    )
");

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS market_price_history (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        crop_name   VARCHAR(100) NOT NULL,
        price_per_kg DECIMAL(10,2) NOT NULL,
        source      VARCHAR(100) DEFAULT 'manual',
        recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_crop_recorded (crop_name, recorded_at)
    )
");

/* ── WFP commodity → our crop_name map ───────────────────────────────────── */
// Keys are commodity names as they appear in WFP/HDX data (case-insensitive match)
const COMMODITY_MAP = [
    'maize (white)'            => 'maize',
    'maize (yellow)'           => 'maize',
    'maize'                    => 'maize',
    'beans (dry)'              => 'beans',
    'beans'                    => 'beans',
    'groundnuts (shelled)'     => 'groundnuts',
    'groundnuts (unshelled)'   => 'groundnuts',
    'groundnuts'               => 'groundnuts',
    'rice (imported)'          => 'rice',
    'rice (local)'             => 'rice',
    'rice'                     => 'rice',
    'cassava (fresh)'          => 'cassava',
    'cassava (dried)'          => 'cassava',
    'cassava'                  => 'cassava',
    'sweet potatoes'           => 'sweet potato',
    'sweet potato'             => 'sweet potato',
    'sorghum (red)'            => 'sorghum',
    'sorghum (white)'          => 'sorghum',
    'sorghum'                  => 'sorghum',
    'pigeon peas'              => 'pigeon peas',
    'pigeon peas (green)'      => 'pigeon peas',
    'soya beans'               => 'soybeans',
    'soya bean'                => 'soybeans',
    'soya'                     => 'soybeans',
    'soybeans'                 => 'soybeans',
    'cowpeas'                  => 'cowpeas',
    'millet'                   => 'millet',
    'sugar (refined)'          => 'sugar',
    'sugar'                    => 'sugar',
    'wheat flour'              => 'wheat',
    'wheat'                    => 'wheat',
    'irish potatoes'           => 'potatoes',
    'potatoes'                 => 'potatoes',
    'tomatoes'                 => 'tomatoes',
    'onions'                   => 'onions',
];

/* ── Unit normalisation: convert price to MWK/kg ─────────────────────────── */
function normalisePriceToKg(float $price, string $unit): float
{
    $u = strtolower(trim($unit));
    if (str_contains($u, '50 kg'))  return $price / 50;
    if (str_contains($u, '90 kg'))  return $price / 90;
    if (str_contains($u, '100 kg')) return $price / 100;
    if (str_contains($u, '25 kg'))  return $price / 25;
    if (str_contains($u, '10 kg'))  return $price / 10;
    if (str_contains($u, '5 kg'))   return $price / 5;
    if (str_contains($u, '2 kg'))   return $price / 2;
    // 'KG', 'kg', '1 KG', 'per kg' → already per kg
    return $price;
}

/* ── HTTP helper (Using cURL for better reliability) ─────────────────────── */
function httpGet(string $url, int $timeout = 30): ?string
{
    if (!function_exists('curl_init')) {
        $ctx = stream_context_create([
            'http' => ['timeout' => $timeout, 'user_agent' => 'NAOS-AgroSystem/1.1 (Malawi; contact@naos.mw)'],
            'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);
        return @file_get_contents($url, false, $ctx);
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'NAOS-AgroSystem/1.1 (Malawi; contact@naos.mw)');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $resp = curl_exec($ch);
    $info = curl_getinfo($ch);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($resp === false || $info['http_code'] >= 400) {
        error_log("NAOS Sync Error [{$info['http_code']}]: {$err} for URL: {$url}");
        return null;
    }
    return (strlen($resp) > 10) ? $resp : null;
}

/* ── Source 1: HDX CKAN API (WFP Global Food Prices) ─────────────────────── */
function fetchFromHDX(): ?array
{
    // WFP Food Prices dataset on HDX — Malawi-specific filter
    $resourceId = '29274f4b-de9e-4a57-8e56-a4a4ac790fb4';
    $url = "https://data.humdata.org/api/3/action/datastore_search"
         . "?resource_id={$resourceId}&limit=1000"
         . "&q=" . urlencode(json_encode(['adm0_name' => 'Malawi']));

    $raw = httpGet($url);
    if (!$raw) return null;

    $data = json_decode($raw, true);
    if (empty($data['success']) || empty($data['result']['records'])) return null;

    return $data['result']['records'];
}

/* ── Source 2: WFP VAM Dataviz API (fallback, no auth needed) ────────────── */
function fetchFromWFPVam(): ?array
{
    // Country code 63 = Malawi in WFP's internal system
    $url = "https://dataviz.vam.wfp.org/api/GetCommodityPriceInCountryForMap"
         . "?CountryCode=63&CommodityId=0";

    $raw = httpGet($url, 15);
    if (!$raw) return null;

    $data = json_decode($raw, true);
    if (!is_array($data)) return null;

    // Normalise VAM format → HDX-like records
    $records = [];
    foreach ($data as $item) {
        $records[] = [
            'cm_name'  => $item['commodityName']  ?? '',
            'mp_price' => $item['price']           ?? 0,
            'um_name'  => $item['unit']            ?? 'KG',
            'pt_name'  => $item['priceType']       ?? 'Retail',
            'mp_year'  => $item['date']            ?? date('Y'),
            'mp_month' => null,
        ];
    }
    return $records;
}

/* ── Aggregate records: get average retail price per commodity ───────────── */
function aggregateByCommmodity(array $records): array
{
    $buckets = [];   // [cropKey => [sum, count, unit, year, month]]

    foreach ($records as $rec) {
        $commodityRaw = strtolower(trim($rec['cm_name']  ?? $rec['commodity'] ?? ''));
        $priceType    = strtolower(trim($rec['pt_name']  ?? $rec['priceType'] ?? 'retail'));
        $price        = (float)($rec['mp_price'] ?? $rec['price'] ?? 0);
        $unit         = trim($rec['um_name']     ?? $rec['unit']  ?? 'KG');
        $year         = (int)($rec['mp_year']    ?? date('Y'));
        $month        = (int)($rec['mp_month']   ?? date('m'));

        // Only retail prices; skip zero/negative
        if ($priceType !== 'retail' || $price <= 0) continue;

        // Map to our crop key
        $cropKey = COMMODITY_MAP[$commodityRaw] ?? null;
        if (!$cropKey) continue;

        $pricePerKg = normalisePriceToKg($price, $unit);

        if (!isset($buckets[$cropKey])) {
            $buckets[$cropKey] = ['sum' => 0, 'count' => 0, 'year' => $year, 'month' => $month];
        }
        $buckets[$cropKey]['sum']   += $pricePerKg;
        $buckets[$cropKey]['count'] += 1;
        // Keep most recent
        if ($year > $buckets[$cropKey]['year'] || ($year === $buckets[$cropKey]['year'] && $month > $buckets[$cropKey]['month'])) {
            $buckets[$cropKey]['year']  = $year;
            $buckets[$cropKey]['month'] = $month;
        }
    }

    // Compute averages
    $result = [];
    foreach ($buckets as $cropKey => $b) {
        if ($b['count'] === 0) continue;
        $result[$cropKey] = [
            'price'  => round($b['sum'] / $b['count'], 2),
            'year'   => $b['year'],
            'month'  => $b['month'],
        ];
    }
    return $result;
}

/* ── Main sync logic ─────────────────────────────────────────────────────── */
$source  = 'WFP/HDX';
$records = fetchFromHDX();

if (!$records) {
    $source  = 'WFP/VAM Dataviz';
    $records = fetchFromWFPVam();
}

if (!$records) {
    // Log failure in DB
    $msg = 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.';
    $stmt = mysqli_prepare($conn, "INSERT INTO wfp_sync_log (synced_at, source, records_found, crops_updated, status, message) VALUES (NOW(), 'WFP/HDX', 0, 0, 'failed', ?)");
    mysqli_stmt_bind_param($stmt, 's', $msg);
    mysqli_stmt_execute($stmt);

    echo json_encode([
        'success'    => false,
        'message'    => $msg,
        'suggestion' => 'The system will continue to use the last verified prices. Please try syncing again in a few hours.',
    ]);
    exit;
}

$aggregated   = aggregateByCommmodity($records);
$cropsUpdated = 0;
$skipped      = [];
$updated      = [];

foreach ($aggregated as $cropKey => $data) {
    $price = $data['price'];

    // 1. Update crop_config.current_price
    $stmt = mysqli_prepare($conn, "UPDATE crop_config SET current_price = ?, price_updated_at = NOW() WHERE LOWER(crop_name) = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ds', $price, $cropKey);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $cropsUpdated++;
            $updated[] = $cropKey;

            // 2. Append to price history
            $histStmt = mysqli_prepare($conn,
                "INSERT INTO market_price_history (crop_name, price_per_kg, source, recorded_at) VALUES (?, ?, ?, NOW())"
            );
            if ($histStmt) {
                mysqli_stmt_bind_param($histStmt, 'sss', $cropKey, $price, $source);
                mysqli_stmt_execute($histStmt);
            }
        } else {
            $skipped[] = $cropKey; // crop not in our crop_config
        }
    }
}

// 3. Log the sync run
$status  = $cropsUpdated > 0 ? 'success' : 'partial';
$logMsg  = "Updated: " . implode(', ', $updated) . ". "
         . (count($skipped) ? "Not matched: " . implode(', ', $skipped) : "");

$logStmt = mysqli_prepare($conn,
    "INSERT INTO wfp_sync_log (synced_at, source, records_found, crops_updated, status, message) VALUES (NOW(), ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($logStmt, 'siiiss', $source, count($records), $cropsUpdated, $status, $logMsg);
mysqli_stmt_execute($logStmt);

echo json_encode([
    'success'       => true,
    'source'        => $source,
    'records_found' => count($records),
    'crops_updated' => $cropsUpdated,
    'updated'       => $updated,
    'skipped'       => $skipped,
    'synced_at'     => date('Y-m-d H:i:s'),
    'message'       => $cropsUpdated > 0
        ? "✅ {$cropsUpdated} crop price(s) updated from {$source}."
        : "⚠️ No matching crops found in WFP data. Prices unchanged.",
]);
?>

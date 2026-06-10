<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

/* ── Auto-sync if prices are stale (>24 h) ────────────────────────────────
 * Checks the most recent wfp_sync_log entry. If it's older than 24 hours
 * (or no sync has ever run) we silently call sync_market_prices.php
 * in the background so the next page load sees fresh data.
 */
$tableExists = mysqli_query($conn, "SHOW TABLES LIKE 'wfp_sync_log'");
if (mysqli_num_rows($tableExists) > 0) {
    $lastSync = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT synced_at FROM wfp_sync_log WHERE status != 'failed' ORDER BY synced_at DESC LIMIT 1"
    ));
    $needsSync = !$lastSync || (time() - strtotime($lastSync['synced_at'])) > 86400; // 24 h

    if ($needsSync) {
        // Non-blocking background call (fire-and-forget)
        $syncUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
                 . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
                 . dirname($_SERVER['SCRIPT_NAME']) . '/sync_market_prices.php?auto=1';
        $ctx = stream_context_create(['http' => ['timeout' => 2]]);
        @file_get_contents($syncUrl, false, $ctx);  // intentionally short timeout
    }
}

/* ── Last sync metadata ────────────────────────────────────────────────────*/
$syncMeta = ['synced_at' => null, 'source' => 'manual'];
if (mysqli_num_rows($tableExists) > 0) {
    $row = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT synced_at, source, crops_updated FROM wfp_sync_log ORDER BY synced_at DESC LIMIT 1"
    ));
    if ($row) $syncMeta = $row;
}

/* ── Fetch prices from crop_config ───────────────────────────────────────── */
$prices = [];
$lastUpdatedAt = [];
$ids = [];
$result = mysqli_query($conn,
    "SELECT id, crop_name, current_price as price_per_kg, price_updated_at
     FROM crop_config WHERE current_price IS NOT NULL ORDER BY crop_name"
);
while ($row = mysqli_fetch_assoc($result)) {
    $prices[$row['crop_name']]        = (float) $row['price_per_kg'];
    $lastUpdatedAt[$row['crop_name']] = $row['price_updated_at'];
    $ids[$row['crop_name']]           = (int) $row['id'];
}

/* ── Price trends ─────────────────────────────────────────────────────────── */
$trends = [];
foreach ($prices as $crop => $currentPrice) {
    $trendSql = "SELECT price_per_kg FROM market_price_history
                 WHERE crop_name = '$crop' ORDER BY recorded_at DESC LIMIT 1 OFFSET 1";
    $tRes     = mysqli_query($conn, $trendSql);
    $prevPrice = ($tRes && mysqli_num_rows($tRes) > 0)
        ? (float) mysqli_fetch_assoc($tRes)['price_per_kg']
        : $currentPrice;

    if ($currentPrice > $prevPrice)      $trends[$crop] = 'rising';
    elseif ($currentPrice < $prevPrice)  $trends[$crop] = 'falling';
    else                                  $trends[$crop] = 'stable';
}

/* ── Demand signals ───────────────────────────────────────────────────────── */
$demand = [];
$dRes = mysqli_query($conn,
    "SELECT cc.crop_name, COUNT(*) as count
     FROM inquiries i
     JOIN produce_listings pl ON i.listing_id = pl.id
     JOIN crop_config cc ON pl.crop_id = cc.id
     GROUP BY cc.crop_name"
);
if ($dRes) {
    while ($row = mysqli_fetch_assoc($dRes)) {
        $demand[$row['crop_name']] = (int) $row['count'];
    }
}

/* ── Best deals ───────────────────────────────────────────────────────────── */
$bestOptions = [];
$bRes = mysqli_query($conn,
    "SELECT id, produce_type, price, quantity FROM produce_listings
     WHERE status = 'available' ORDER BY price ASC"
);
if ($bRes) {
    while ($row = mysqli_fetch_assoc($bRes)) {
        if (!isset($bestOptions[$row['produce_type']])) {
            $bestOptions[$row['produce_type']] = $row;
        }
    }
}

$maizeTrend = $trends['maize'] ?? 'stable';
$report     = "Market Update: Maize prices are {$maizeTrend}. High interest in: "
            . (count($demand) ? implode(', ', array_keys($demand)) : 'various crops');

echo json_encode([
    'prices'       => $prices,
    'ids'          => $ids,
    'trends'       => $trends,
    'demand'       => $demand,
    'best_options' => array_values($bestOptions),
    'report'       => $report,
    'last_updated' => $lastUpdatedAt,
    'sync_meta'    => $syncMeta,
]);
?>
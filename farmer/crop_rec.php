<?php
error_reporting(0);
ini_set('display_errors', 0);
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if (!checkSubscription($conn, $user_id)) {
    echo json_encode(['recommendation' => '<div style="color:#856404; background:#fff3cd; padding:1rem; border-radius:6px;"><i class="fa-solid fa-lock"></i> Please subscribe to access personalized crop recommendations.</div>']);
    exit;
}

if (!function_exists('curl_init')) {
    echo json_encode(['recommendation' => 'Server configuration error: cURL not enabled.']);
    exit;
}

$farm_id = $_GET['farm_id'] ?? null;
$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;

$farm_district     = '';
$farm_soil         = '';
$farm_eco_zone     = '';
$farm_name_label   = '';

// --- Fetch farm details ---
if ($farm_id) {
    $stmt = mysqli_prepare($conn, "SELECT farm_name, district, soil_type, ecological_zone, latitude, longitude FROM farms WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $farm_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $farm = mysqli_fetch_assoc($result);

    if ($farm) {
        $lat             = $farm['latitude'];
        $lon             = $farm['longitude'];
        $farm_district   = $farm['district'];
        $farm_soil       = $farm['soil_type'];
        $farm_eco_zone   = $farm['ecological_zone'];
        $farm_name_label = $farm['farm_name'];
    }
}

// --- Fallback to user's home location ---
if (!$lat || !$lon) {
    $stmt = mysqli_prepare($conn, "SELECT location FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $location = $user ? $user['location'] : '-13.2543,34.3015';

    $parts = explode(',', $location);
    if (count($parts) === 2 && is_numeric(trim($parts[0])) && is_numeric(trim($parts[1]))) {
        $lat = trim($parts[0]);
        $lon = trim($parts[1]);
    } else {
        $lat = -13.2543;
        $lon = 34.3015;
    }
}

// --- Fetch weather forecast ---
$url = "https://api.open-meteo.com/v1/forecast?latitude=$lat&longitude=$lon&current_weather=true&daily=temperature_2m_max,temperature_2m_min,precipitation_sum&forecast_days=10&timezone=Africa/Nairobi";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 || $response === false) {
    echo json_encode(['recommendation' => '<p style="color:red;"><i class="fa-solid fa-triangle-exclamation"></i> Weather data is currently unavailable. Please try again later.</p>']);
    exit;
}

$data        = json_decode($response, true);
$daily       = $data['daily'];
$total_precip = array_sum($daily['precipitation_sum']);
$avg_max_temp = array_sum($daily['temperature_2m_max']) / count($daily['temperature_2m_max']);
$avg_min_temp = array_sum($daily['temperature_2m_min']) / count($daily['temperature_2m_min']);
$avg_temp     = ($avg_max_temp + $avg_min_temp) / 2;

// Soil profile descriptions
$soil_profiles = [
    'Ferruginous'  => 'well-draining red soils with moderate fertility, good for a wide range of crops with adequate fertilization',
    'Alluvial'     => 'rich, naturally fertile soils found along rivers and lake shores, excellent water retention',
    'Lithosols'    => 'shallow, stony soils typical of highland areas, low water retention but suitable for drought-tolerant varieties',
    'Vertisols'    => 'heavy black cotton soils with high clay content, excellent water retention, prone to waterlogging',
    'Sandy'        => 'light, fast-draining soils with low natural fertility, best suited for crops tolerant of dry conditions',
];

$soil_desc = isset($soil_profiles[$farm_soil]) ? $soil_profiles[$farm_soil] : 'general purpose soils';

// --- Build advisory header ---
$rec = '';
if ($farm_name_label) {
    $rec .= "<div style='margin-bottom:1rem; padding:0.75rem 1rem; background:#f0fdf4; border-left:4px solid #22c55e; border-radius:4px;'>";
    $rec .= "<div style='font-weight:700; color:#166534; margin-bottom:0.25rem;'><i class='fa-solid fa-tractor'></i> Advisory for: {$farm_name_label}</div>";
    if ($farm_district) $rec .= "<div style='font-size:0.85rem; color:#555;'><i class='fa-solid fa-location-dot'></i> {$farm_district}" . ($farm_eco_zone ? " &bull; {$farm_eco_zone} Zone" : "") . "</div>";
    if ($farm_soil)     $rec .= "<div style='font-size:0.85rem; color:#555; margin-top:0.2rem;'><i class='fa-solid fa-layer-group'></i> Soil: <strong>{$farm_soil}</strong> &mdash; {$soil_desc}.</div>";
    $rec .= "</div>";
} else {
    $rec .= "<div style='margin-bottom:1rem; padding:0.75rem 1rem; background:#eff6ff; border-left:4px solid #3b82f6; border-radius:4px; font-size:0.85rem; color:#1e40af;'>";
    $rec .= "<i class='fa-solid fa-circle-info'></i> No specific farm selected. Showing general advisory for your home location. <strong>Register a farm with its soil type for a more precise recommendation.</strong>";
    $rec .= "</div>";
}

// --- Climate summary box ---
$rain_level = $total_precip < 10 ? 'Very Dry' : ($total_precip < 30 ? 'Dry' : ($total_precip < 60 ? 'Moderate' : 'Wet'));
$rain_color = $total_precip < 10 ? '#dc2626' : ($total_precip < 30 ? '#f59e0b' : ($total_precip < 60 ? '#16a34a' : '#2563eb'));
$rec .= "<div style='display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;'>";
$rec .= "  <div style='flex:1; min-width:120px; text-align:center; padding:0.75rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;'>";
$rec .= "    <div style='font-size:1.5rem; color:{$rain_color};'><i class='fa-solid fa-cloud-rain'></i></div>";
$rec .= "    <div style='font-weight:700; font-size:1rem;'>" . number_format($total_precip, 1) . "mm</div>";
$rec .= "    <div style='font-size:0.75rem; color:#64748b;'>Rainfall (10-day) &bull; {$rain_level}</div>";
$rec .= "  </div>";
$rec .= "  <div style='flex:1; min-width:120px; text-align:center; padding:0.75rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;'>";
$rec .= "    <div style='font-size:1.5rem; color:#f59e0b;'><i class='fa-solid fa-temperature-half'></i></div>";
$rec .= "    <div style='font-weight:700; font-size:1rem;'>" . number_format($avg_max_temp, 1) . "°C / " . number_format($avg_min_temp, 1) . "°C</div>";
$rec .= "    <div style='font-size:0.75rem; color:#64748b;'>Avg High / Low Temp</div>";
$rec .= "  </div>";
if ($farm_soil) {
    $rec .= "  <div style='flex:1; min-width:120px; text-align:center; padding:0.75rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;'>";
    $rec .= "    <div style='font-size:1.5rem; color:#78350f;'><i class='fa-solid fa-layer-group'></i></div>";
    $rec .= "    <div style='font-weight:700; font-size:0.95rem;'>{$farm_soil}</div>";
    $rec .= "    <div style='font-size:0.75rem; color:#64748b;'>Soil Type</div>";
    $rec .= "  </div>";
}
if ($farm_eco_zone) {
    $rec .= "  <div style='flex:1; min-width:120px; text-align:center; padding:0.75rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;'>";
    $rec .= "    <div style='font-size:1.5rem; color:#0891b2;'><i class='fa-solid fa-mountain-sun'></i></div>";
    $rec .= "    <div style='font-weight:700; font-size:0.85rem;'>{$farm_eco_zone}</div>";
    $rec .= "    <div style='font-size:0.75rem; color:#64748b;'>Ecological Zone</div>";
    $rec .= "  </div>";
}
$rec .= "</div>";

// --- Query crops that match weather ---
$sql = "SELECT crop_name, ideal_soil, ideal_ecological_zones, ideal_districts, drought_resistant, current_price
        FROM crop_config
        WHERE ? BETWEEN min_rainfall AND max_rainfall
          AND ? BETWEEN min_temp AND max_temp";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "dd", $total_precip, $avg_temp);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$prime_crops  = []; // Matched weather + soil/zone/district
$viable_crops = []; // Matched weather only

while ($row = mysqli_fetch_assoc($result)) {
    $cn = ucfirst($row['crop_name']);
    $reasons = [];
    $score   = 0;

    // Soil match
    if ($farm_soil && !empty($row['ideal_soil'])) {
        $soils = array_map('trim', explode(',', $row['ideal_soil']));
        foreach ($soils as $s) {
            if (stripos($farm_soil, $s) !== false || stripos($s, $farm_soil) !== false) {
                $reasons[] = "<span style='color:#166534;'><i class='fa-solid fa-layer-group'></i> Suited to <strong>{$farm_soil}</strong> soil</span>";
                $score += 3;
                break;
            }
        }
    }

    // Ecological zone match
    if ($farm_eco_zone && !empty($row['ideal_ecological_zones'])) {
        $zones = array_map('trim', explode(',', $row['ideal_ecological_zones']));
        foreach ($zones as $z) {
            if (stripos($farm_eco_zone, $z) !== false || stripos($z, $farm_eco_zone) !== false) {
                $reasons[] = "<span style='color:#0369a1;'><i class='fa-solid fa-mountain-sun'></i> Thrives in <strong>{$farm_eco_zone}</strong> zone</span>";
                $score += 2;
                break;
            }
        }
    }

    // District match
    if ($farm_district && !empty($row['ideal_districts'])) {
        $districts = array_map('trim', explode(',', $row['ideal_districts']));
        foreach ($districts as $d) {
            if (stripos($farm_district, $d) !== false || stripos($d, $farm_district) !== false) {
                $reasons[] = "<span style='color:#7c3aed;'><i class='fa-solid fa-location-dot'></i> Historically grown in <strong>{$farm_district}</strong></span>";
                $score += 1;
                break;
            }
        }
    }

    // Price info
    $price_note = $row['current_price'] > 0 ? " &mdash; <em>MWK " . number_format($row['current_price'], 0) . "/kg</em>" : '';

    if ($score >= 2) {
        $prime_crops[] = [
            'name'    => $cn,
            'reasons' => $reasons,
            'price'   => $price_note,
            'score'   => $score,
        ];
    } else {
        $viable_crops[] = $cn . $price_note;
    }
}

// Sort prime crops by score descending
usort($prime_crops, fn($a, $b) => $b['score'] - $a['score']);

// --- Historical performance ---
$histStmt = mysqli_prepare($conn, "SELECT crop_name FROM farm_activities WHERE user_id = ? AND activity_type = 'harvested' AND quantity > 0 ORDER BY date DESC LIMIT 10");
mysqli_stmt_bind_param($histStmt, "i", $user_id);
mysqli_stmt_execute($histStmt);
$histResult = mysqli_stmt_get_result($histStmt);
$proven_crops = [];
$all_viable_names = array_merge(
    array_column($prime_crops, 'name'),
    array_map(fn($c) => strip_tags(explode(' &mdash;', $c)[0]), $viable_crops)
);
while ($h = mysqli_fetch_assoc($histResult)) {
    $hc = ucfirst($h['crop_name']);
    if (in_array($hc, $all_viable_names) && !in_array($hc, $proven_crops)) {
        $proven_crops[] = $hc;
    }
}

// --- Render results ---
if (!empty($prime_crops)) {
    $rec .= "<div style='margin-bottom:1rem;'>";
    $rec .= "  <div style='font-weight:700; color:#166534; margin-bottom:0.5rem; font-size:0.95rem;'><i class='fa-solid fa-star' style='color:#f59e0b;'></i> Prime Recommendations &mdash; Best fit for your conditions:</div>";
    foreach ($prime_crops as $crop) {
        $rec .= "<div style='padding:0.75rem 1rem; border:1px solid #bbf7d0; background:#f0fdf4; border-radius:8px; margin-bottom:0.5rem;'>";
        $rec .= "  <div style='font-weight:700; font-size:1rem; color:#14532d;'>{$crop['name']}{$crop['price']}</div>";
        $rec .= "  <div style='margin-top:0.3rem; font-size:0.82rem; display:flex; flex-wrap:wrap; gap:0.5rem;'>" . implode(' ', $crop['reasons']) . "</div>";
        $rec .= "</div>";
    }
    $rec .= "</div>";
}

if (!empty($viable_crops)) {
    $label = !empty($prime_crops) ? 'Other Viable Alternatives (weather-matched):' : 'Viable Crops for Current Weather:';
    $rec .= "<div style='padding:0.75rem 1rem; background:#fafafa; border:1px solid #e2e8f0; border-radius:8px; margin-bottom:1rem;'>";
    $rec .= "<div style='font-weight:600; font-size:0.875rem; margin-bottom:0.4rem;'><i class='fa-solid fa-check-circle' style='color:#22c55e;'></i> {$label}</div>";
    $rec .= "<div style='font-size:0.875rem; color:#374151;'>" . implode(' &bull; ', $viable_crops) . "</div>";
    $rec .= "</div>";
}

if (empty($prime_crops) && empty($viable_crops)) {
    if ($total_precip < 20) {
        $sql_drought = "SELECT crop_name FROM crop_config WHERE drought_resistant = 1";
        $res_drought = mysqli_query($conn, $sql_drought);
        $dc = [];
        while ($d = mysqli_fetch_assoc($res_drought)) $dc[] = ucfirst($d['crop_name']);
        $rec .= "<div style='padding:1rem; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; margin-bottom:1rem;'>";
        $rec .= "  <div style='font-weight:700; color:#dc2626; margin-bottom:0.5rem;'><i class='fa-solid fa-droplet-slash'></i> Severe Dry Condition Warning</div>";
        $rec .= "  <p style='font-size:0.875rem; margin:0;'>Only drought-resistant varieties are advisable: <strong>" . implode(', ', $dc) . "</strong>. Supplement with irrigation where possible.</p>";
        $rec .= "</div>";
    } else {
        $rec .= "<div style='padding:1rem; background:#fffbeb; border:1px solid #fde68a; border-radius:8px;'>";
        $rec .= "  <i class='fa-solid fa-triangle-exclamation' style='color:#d97706;'></i> No strong crop match for these specific conditions. Consult your local agricultural extension officer for tailored guidance.";
        $rec .= "</div>";
    }
}

if (!empty($proven_crops)) {
    $rec .= "<div style='margin-top:0.75rem; padding:0.75rem 1rem; background:#f5f3ff; border:1px solid #ddd6fe; border-radius:8px; font-size:0.85rem;'>";
    $rec .= "  <i class='fa-solid fa-clock-rotate-left' style='color:#7c3aed;'></i> <strong>Your Track Record:</strong> You have successfully harvested <strong>" . implode(', ', $proven_crops) . "</strong> in similar conditions — strong candidates for this season.";
    $rec .= "</div>";
}

// --- Soil-specific tips ---
$soil_tips = [
    'Ferruginous' => 'Apply NPK fertilizer before planting and consider lime if soil acidity is high (pH below 5.5).',
    'Alluvial'    => 'Ensure adequate drainage to avoid waterlogging. Soils are naturally fertile — reduce nitrogen applications accordingly.',
    'Lithosols'   => 'Use conservation tillage and mulching to retain moisture. Focus on drought-tolerant, shallow-rooted crops.',
    'Vertisols'   => 'Plant on raised beds or ridges to manage waterlogging risk. Avoid tillage when wet — work soil only when moist.',
    'Sandy'       => 'Apply organic matter and mulch to improve water retention. Split fertilizer applications to reduce leaching.',
];

if ($farm_soil && isset($soil_tips[$farm_soil])) {
    $rec .= "<div style='margin-top:0.75rem; padding:0.75rem 1rem; background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; font-size:0.85rem;'>";
    $rec .= "  <i class='fa-solid fa-flask' style='color:#ea580c;'></i> <strong>Soil Management Tip for {$farm_soil} Soil:</strong> {$soil_tips[$farm_soil]}";
    $rec .= "</div>";
}

echo json_encode(['recommendation' => $rec]);
?>
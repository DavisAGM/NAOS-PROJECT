<?php
error_reporting(0);
ini_set('display_errors', 0);
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['advice' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if (!checkSubscription($conn, $user_id)) {
    echo json_encode(['advice' => '<div style="color:#856404; background:#fff3cd; padding:1rem; border-radius:6px;"><i class="fa-solid fa-lock"></i> This feature is only available for active subscribers.</div>']);
    exit;
}

if (!function_exists('curl_init')) {
    echo json_encode(['advice' => 'Server configuration error: cURL not enabled.']);
    exit;
}

$crop = strtolower(trim($_GET['crop'] ?? ''));
$qty  = intval($_GET['qty'] ?? 0);

if (!$crop) {
    echo json_encode(['advice' => '<p style="color:#6b7280;">Please select a crop to evaluate.</p>']);
    exit;
}

// --- Fetch user location ---
$stmt = mysqli_prepare($conn, "SELECT location FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
$location = $user ? $user['location'] : '-13.2543,34.3015';

$parts = explode(',', $location);
if (count($parts) === 2 && is_numeric(trim($parts[0])) && is_numeric(trim($parts[1]))) {
    $lat = trim($parts[0]);
    $lon = trim($parts[1]);
} else {
    $lat = -13.2543;
    $lon = 34.3015;
}

// --- Fetch best farm for this user (most recent with soil data) ---
$farm_stmt = mysqli_prepare($conn, "SELECT farm_name, district, soil_type, ecological_zone FROM farms WHERE user_id = ? AND soil_type IS NOT NULL AND soil_type != '' ORDER BY created_at DESC LIMIT 1");
mysqli_stmt_bind_param($farm_stmt, "i", $user_id);
mysqli_stmt_execute($farm_stmt);
$farm_result = mysqli_stmt_get_result($farm_stmt);
$farm = mysqli_fetch_assoc($farm_result);
$farm_soil     = $farm ? $farm['soil_type'] : '';
$farm_district = $farm ? $farm['district'] : '';
$farm_eco_zone = $farm ? $farm['ecological_zone'] : '';
$farm_label    = $farm ? $farm['farm_name'] : '';

// --- Fetch crop config ---
$stmt = mysqli_prepare($conn, "SELECT * FROM crop_config WHERE LOWER(crop_name) = ?");
mysqli_stmt_bind_param($stmt, "s", $crop);
mysqli_stmt_execute($stmt);
$config = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$config) {
    echo json_encode(['advice' => "<p style='color:#dc2626;'><i class='fa-solid fa-circle-xmark'></i> Crop \"" . ucfirst($crop) . "\" not found in our database.</p>"]);
    exit;
}

// --- Fetch weather ---
$url = "https://api.open-meteo.com/v1/forecast?latitude=$lat&longitude=$lon&current_weather=true&daily=temperature_2m_max,temperature_2m_min,precipitation_sum&forecast_days=10&timezone=Africa/Nairobi";
$ch  = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 || $response === false) {
    echo json_encode(['advice' => '<p style="color:red;"><i class="fa-solid fa-triangle-exclamation"></i> Weather data unavailable. Please try again later.</p>']);
    exit;
}

$wdata        = json_decode($response, true);
$total_precip = array_sum($wdata['daily']['precipitation_sum']);
$avg_max_temp = array_sum($wdata['daily']['temperature_2m_max']) / count($wdata['daily']['temperature_2m_max']);
$avg_min_temp = array_sum($wdata['daily']['temperature_2m_min']) / count($wdata['daily']['temperature_2m_min']);
$avg_temp     = ($avg_max_temp + $avg_min_temp) / 2;

// --- Evaluation ---
$rain_ok   = ($total_precip >= $config['min_rainfall'] && $total_precip <= $config['max_rainfall']);
$temp_ok   = ($avg_temp >= $config['min_temp'] && $avg_temp <= $config['max_temp']);
$market_price = floatval($config['current_price']);

// Soil suitability check
$soil_ok       = false;
$soil_reason   = '';
if ($farm_soil && !empty($config['ideal_soil'])) {
    $ideal_soils = array_map('trim', explode(',', $config['ideal_soil']));
    foreach ($ideal_soils as $s) {
        if (stripos($farm_soil, $s) !== false || stripos($s, $farm_soil) !== false) {
            $soil_ok     = true;
            $soil_reason = "your <strong>{$farm_soil}</strong> soil is among the optimal types for this crop";
            break;
        }
    }
    if (!$soil_ok) {
        $soil_reason = "this crop prefers <strong>" . implode(', ', $ideal_soils) . "</strong> soils, while your farm has <strong>{$farm_soil}</strong> soil";
    }
}

// Eco zone check
$zone_ok     = false;
$zone_reason = '';
if ($farm_eco_zone && !empty($config['ideal_ecological_zones'])) {
    $ideal_zones = array_map('trim', explode(',', $config['ideal_ecological_zones']));
    foreach ($ideal_zones as $z) {
        if (stripos($farm_eco_zone, $z) !== false || stripos($z, $farm_eco_zone) !== false) {
            $zone_ok     = true;
            $zone_reason = "the <strong>{$farm_eco_zone}</strong> zone is well-suited to this crop";
            break;
        }
    }
    if (!$zone_ok) {
        $zone_reason = "this crop is typically grown in <strong>" . implode(', ', $ideal_zones) . "</strong> zones";
    }
}

// Overall feasibility score (0-4)
$score = 0;
if ($rain_ok)  $score++;
if ($temp_ok)  $score++;
if ($soil_ok)  $score++;
if ($zone_ok)  $score++;

$overall_ok = $score >= 3;

// Colour theming
$header_bg    = $overall_ok ? '#f0fdf4' : '#fef9ef';
$header_border= $overall_ok ? '#22c55e' : '#f59e0b';
$verdict_color= $overall_ok ? '#166534' : '#92400e';
$verdict_icon = $overall_ok ? 'fa-circle-check' : 'fa-triangle-exclamation';
$verdict_text = $overall_ok
    ? "Good Conditions — Recommended to Plant"
    : ($score <= 1 ? "Poor Conditions — Not Recommended" : "Marginal Conditions — Proceed with Caution");

$cn = ucfirst($crop);
$advice = '';

// --- Header ---
$advice .= "<div style='padding:0.75rem 1rem; background:{$header_bg}; border-left:4px solid {$header_border}; border-radius:6px; margin-bottom:1rem;'>";
$advice .= "  <div style='font-weight:700; font-size:1rem; color:{$verdict_color};'><i class='fa-solid {$verdict_icon}'></i> {$cn}: {$verdict_text}</div>";
if ($farm_label) $advice .= "  <div style='font-size:0.8rem; color:#6b7280; margin-top:0.2rem;'><i class='fa-solid fa-tractor'></i> Based on your farm: <strong>{$farm_label}</strong>" . ($farm_district ? " &bull; {$farm_district}" : "") . "</div>";
$advice .= "</div>";

// --- Condition grid ---
$advice .= "<div style='display:grid; grid-template-columns:repeat(auto-fit, minmax(130px,1fr)); gap:0.5rem; margin-bottom:1rem;'>";

// Rain
$ri = $rain_ok ? '#22c55e' : '#ef4444';
$advice .= "<div style='text-align:center; padding:0.65rem; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;'>";
$advice .= "  <div style='color:{$ri}; font-size:1.3rem;'><i class='fa-solid fa-" . ($rain_ok ? 'check' : 'xmark') . "'></i></div>";
$advice .= "  <div style='font-size:0.8rem; color:#374151;'>Rainfall<br><strong>" . number_format($total_precip, 1) . "mm</strong></div>";
$advice .= "  <div style='font-size:0.7rem; color:#6b7280;'>Need: {$config['min_rainfall']}–{$config['max_rainfall']}mm</div>";
$advice .= "</div>";

// Temp
$ti = $temp_ok ? '#22c55e' : '#ef4444';
$advice .= "<div style='text-align:center; padding:0.65rem; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;'>";
$advice .= "  <div style='color:{$ti}; font-size:1.3rem;'><i class='fa-solid fa-" . ($temp_ok ? 'check' : 'xmark') . "'></i></div>";
$advice .= "  <div style='font-size:0.8rem; color:#374151;'>Temperature<br><strong>" . number_format($avg_temp, 1) . "°C</strong></div>";
$advice .= "  <div style='font-size:0.7rem; color:#6b7280;'>Need: {$config['min_temp']}–{$config['max_temp']}°C</div>";
$advice .= "</div>";

// Soil
if ($farm_soil) {
    $si = $soil_ok ? '#22c55e' : '#ef4444';
    $advice .= "<div style='text-align:center; padding:0.65rem; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;'>";
    $advice .= "  <div style='color:{$si}; font-size:1.3rem;'><i class='fa-solid fa-" . ($soil_ok ? 'check' : 'xmark') . "'></i></div>";
    $advice .= "  <div style='font-size:0.8rem; color:#374151;'>Soil Type<br><strong>{$farm_soil}</strong></div>";
    $advice .= "  <div style='font-size:0.7rem; color:#6b7280;'>Ideal: " . implode(', ', array_slice(array_map('trim', explode(',', $config['ideal_soil'] ?: 'General')), 0, 2)) . "</div>";
    $advice .= "</div>";
}

// Eco zone
if ($farm_eco_zone) {
    $zi = $zone_ok ? '#22c55e' : '#f59e0b';
    $advice .= "<div style='text-align:center; padding:0.65rem; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;'>";
    $advice .= "  <div style='color:{$zi}; font-size:1.3rem;'><i class='fa-solid fa-" . ($zone_ok ? 'check' : 'minus') . "'></i></div>";
    $advice .= "  <div style='font-size:0.8rem; color:#374151;'>Eco Zone<br><strong style='font-size:0.75rem;'>{$farm_eco_zone}</strong></div>";
    $advice .= "  <div style='font-size:0.7rem; color:#6b7280;'>Status: " . ($zone_ok ? 'Suitable' : 'Check') . "</div>";
    $advice .= "</div>";
}

$advice .= "</div>";

// --- Narrative detail ---
$advice .= "<div style='font-size:0.875rem; color:#374151; line-height:1.6; margin-bottom:1rem; padding:0.75rem 1rem; background:#f9fafb; border-radius:6px; border:1px solid #e5e7eb;'>";
$advice .= "<strong>Analysis:</strong> ";
$advice .= $rain_ok ? "Rainfall of " . number_format($total_precip, 1) . "mm is within the acceptable range for {$cn}. " : "Rainfall of " . number_format($total_precip, 1) . "mm falls <em>outside</em> the ideal range — consider irrigation or a drought-tolerant variety. ";
$advice .= $temp_ok ? "Temperatures averaging " . number_format($avg_temp, 1) . "°C are well-suited for growth. " : "Temperature of " . number_format($avg_temp, 1) . "°C may stress this crop — monitor for heat/cold damage. ";
if ($farm_soil)     $advice .= ucfirst($soil_reason) . ". ";
if ($farm_eco_zone) $advice .= ucfirst($zone_reason) . ". ";
$advice .= "</div>";

// --- Market & income ---
if ($market_price > 0) {
    $advice .= "<div style='padding:0.75rem 1rem; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; margin-bottom:1rem; font-size:0.875rem;'>";
    $advice .= "  <div style='font-weight:700; color:#1e40af; margin-bottom:0.3rem;'><i class='fa-solid fa-money-bill-trend-up'></i> Market Intelligence</div>";
    $advice .= "  Current market price: <strong>MWK " . number_format($market_price, 0) . "/kg</strong>.";
    if ($qty > 0) {
        $income_est = $qty * $market_price;
        $advice .= " If you harvest <strong>{$qty} kg</strong>, estimated revenue is <strong>MWK " . number_format($income_est, 0) . "</strong>.";
        $advice .= " <em>Subtract input costs (seeds, fertilizer, labour) to determine net profit.</em>";
    }
    $advice .= "</div>";
}

// --- Soil management tip ---
$soil_tips = [
    'Ferruginous' => 'Apply NPK fertilizer pre-planting; lime if pH < 5.5. Top-dress with urea at knee-high stage.',
    'Alluvial'    => 'Naturally fertile — reduce nitrogen. Ensure drainage to prevent root rot and fungal diseases.',
    'Lithosols'   => 'Use mulching and conservation tillage to retain moisture. Avoid deep tillage on thin topsoil.',
    'Vertisols'   => 'Plant on ridges or raised beds. Avoid heavy machinery on wet soil to prevent compaction.',
    'Sandy'       => 'Add compost and organic matter annually. Split fertilizer into 2–3 smaller applications to reduce leaching.',
];

if ($farm_soil && isset($soil_tips[$farm_soil])) {
    $advice .= "<div style='padding:0.75rem 1rem; background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; font-size:0.85rem;'>";
    $advice .= "  <i class='fa-solid fa-flask' style='color:#ea580c;'></i> <strong>Soil Tip ({$farm_soil}):</strong> {$soil_tips[$farm_soil]}";
    $advice .= "</div>";
}

echo json_encode(['advice' => $advice]);
?>
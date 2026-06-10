<?php
error_reporting(0);
ini_set('display_errors', 0);
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!function_exists('curl_init')) {
    echo json_encode(['error' => 'Curl not enabled']);
    exit;
}

$user_id = $_SESSION['user_id'];
$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;

if (!$lat || !$lon) {
    $stmt = mysqli_prepare($conn, "SELECT location FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $location = $user ? $user['location'] : '-13.2543,34.3015';

    // Handle location parsing safely
    $parts = explode(',', $location);
    if (count($parts) === 2) {
        list($lat, $lon) = $parts;
    } else {
        // If location is invalid (e.g. city name "Mzuzu"), fallback to default
        $lat = -13.2543;
        $lon = 34.3015;
    }
}

// Validate coordinates
if (!is_numeric($lat) || !is_numeric($lon) || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
    echo json_encode(['error' => 'Invalid location coordinates']);
    exit;
}

// Open-Meteo API via native PHP cURL
$url = "https://api.open-meteo.com/v1/forecast?latitude=$lat&longitude=$lon&current_weather=true&daily=temperature_2m_max,temperature_2m_min,precipitation_sum&forecast_days=7&timezone=Africa/Nairobi";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['error' => 'Weather API request failed: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['error' => 'Weather API returned status: ' . $http_code]);
    exit;
}

$data = json_decode($response, true);
if (!$data) {
    echo json_encode(['error' => 'Invalid JSON response from Weather API']);
    exit;
}

// Add alert for heavy rain
$alert = null;
if (isset($data['daily']['precipitation_sum'][0]) && $data['daily']['precipitation_sum'][0] > 10) {
    $alert = 'Heavy rain expected!';
}

echo json_encode(array_merge($data, ['alert' => $alert]));
?>
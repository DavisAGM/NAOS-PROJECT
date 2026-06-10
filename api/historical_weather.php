<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-33 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d', strtotime('-3 days'));
$farm_id = $_GET['farm_id'] ?? null;

if (!$lat || !$lon) {
    if ($farm_id) {
        $stmt = mysqli_prepare($conn, "SELECT location FROM farms WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $farm_id, $user_id);
    } else {
        $stmt = mysqli_prepare($conn, "SELECT location FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $location = $row ? $row['location'] : '-13.2543,34.3015';
    
    $parts = explode(',', $location);
    if (count($parts) === 2) {
        list($lat, $lon) = $parts;
    } else {
        $lat = -13.2543;
        $lon = 34.3015;
    }
}

// Open-Meteo Archive API
$url = "https://archive-api.open-meteo.com/v1/archive?latitude=$lat&longitude=$lon&start_date=$start_date&end_date=$end_date&daily=temperature_2m_max,temperature_2m_min,precipitation_sum&timezone=Africa/Nairobi";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['error' => 'Weather Archive API returned status: ' . $http_code]);
    exit;
}

echo $response;
?>

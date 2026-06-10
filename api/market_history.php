<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$crop = isset($_GET['crop']) ? mysqli_real_escape_with_like($_GET['crop'], $conn) : 'maize';
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;

// Function to escape string for SQL
function mysqli_real_escape_with_like($str, $conn) {
    return mysqli_real_escape_string($conn, $str);
}

$history = [];
$query = "SELECT price_per_kg, recorded_at 
          FROM market_price_history 
          WHERE crop_name = '$crop' 
          AND recorded_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
          ORDER BY recorded_at ASC";

$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $history[] = [
            'price' => (float)$row['price_per_kg'],
            'date' => date('M d', strtotime($row['recorded_at']))
        ];
    }
}

// Also get current price to append as the last point
$currentRes = mysqli_query($conn, "SELECT current_price, price_updated_at FROM crop_config WHERE crop_name = '$crop'");
if ($currentRes && $row = mysqli_fetch_assoc($currentRes)) {
    $history[] = [
        'price' => (float)$row['current_price'],
        'date' => date('M d', strtotime($row['price_updated_at']))
    ];
}

echo json_encode([
    'success' => true,
    'crop' => $crop,
    'history' => $history
]);
?>

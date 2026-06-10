<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn() || getUserRole() !== 'farmer') {
    echo json_encode(['error' => 'You need to be logged in as a farmer to do this.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

// Get form data
$farm_id = isset($data['farm_id']) && is_numeric($data['farm_id']) ? intval($data['farm_id']) : null;
$crop_name = mysqli_real_escape_string($conn, $data['crop_name'] ?? $_POST['crop_name'] ?? '');
$activity_type = mysqli_real_escape_string($conn, $data['activity_type'] ?? $_POST['activity_type'] ?? '');
$date = mysqli_real_escape_string($conn, $data['date'] ?? $_POST['date'] ?? '');
$quantity = isset($data['quantity']) && $data['quantity'] !== '' ? floatval($data['quantity']) : null;
$unit = mysqli_real_escape_string($conn, $data['unit'] ?? $_POST['unit'] ?? '');
$notes = mysqli_real_escape_string($conn, $data['notes'] ?? $_POST['notes'] ?? '');

// Validate required fields
if (empty($farm_id) || empty($crop_name) || empty($activity_type) || empty($date)) {
    echo json_encode(['success' => false, 'error' => 'Farm, crop name, activity type, and date are required.']);
    exit;
}

// Validate activity type
$valid_activities = ['planted', 'weeded', 'fertilized', 'sprayed', 'harvested'];
if (!in_array($activity_type, $valid_activities)) {
    echo json_encode(['success' => false, 'error' => 'Invalid activity type.']);
    exit;
}

// Validate date - must not be in the future
$today = date('Y-m-d');
if ($date > $today) {
    echo json_encode(['success' => false, 'error' => 'Activity date cannot be in the future.']);
    exit;
}

// For harvested activities, quantity is required
if ($activity_type === 'harvested' && ($quantity === null || $quantity <= 0)) {
    echo json_encode(['success' => false, 'error' => 'Quantity is required for harvest activities.']);
    exit;
}

// Insert into farm_activities table
$stmt = mysqli_prepare($conn, "INSERT INTO farm_activities (user_id, farm_id, crop_name, activity_type, date, quantity, unit, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "iisssdss", $user_id, $farm_id, $crop_name, $activity_type, $date, $quantity, $unit, $notes);

if (mysqli_stmt_execute($stmt)) {
    $activity_id = mysqli_insert_id($conn);

    // If this is a harvest activity, calculate estimated income
    $estimated_income = null;
    if ($activity_type === 'harvested' && $quantity > 0) {
        // Get current market price for this crop from crop_config
        $price_stmt = mysqli_prepare($conn, "SELECT current_price as price_per_kg FROM crop_config WHERE crop_name = ?");
        mysqli_stmt_bind_param($price_stmt, "s", $crop_name);
        mysqli_stmt_execute($price_stmt);
        $price_result = mysqli_stmt_get_result($price_stmt);

        if ($price_row = mysqli_fetch_assoc($price_result)) {
            $price_per_kg = floatval($price_row['price_per_kg']);
            // Assuming unit is kg or bags (1 bag = 50kg for estimation)
            $kg_amount = ($unit === 'bags') ? $quantity * 50 : $quantity;
            $estimated_income = $kg_amount * $price_per_kg;
        }
    }

    echo json_encode([
        'success' => true,
        'activity_id' => $activity_id,
        'message' => 'Activity recorded successfully.',
        'estimated_income' => $estimated_income
    ]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}
?>
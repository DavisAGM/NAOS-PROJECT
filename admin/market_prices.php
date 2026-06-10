<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'get') {
    $result = mysqli_query($conn, "SELECT id, crop_name, current_price as price_per_kg, price_updated_at as last_updated FROM crop_config ORDER BY crop_name ASC");
    $prices = [];

    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['price_per_kg'] === null) {
            $row['price_per_kg'] = 0.00;
        }
        $prices[] = $row;
    }

    echo json_encode(['success' => true, 'prices' => $prices]);
    exit;
}

if ($action === 'update') {
    $crop_id = intval($_POST['crop_id'] ?? 0);
    $new_price = floatval($_POST['price'] ?? 0);

    if ($crop_id <= 0 || $new_price < 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid price or crop ID']);
        exit;
    }

    $stmt = mysqli_prepare($conn, "UPDATE crop_config SET current_price = ?, price_updated_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "di", $new_price, $crop_id);

    if (mysqli_stmt_execute($stmt)) {
        $getCropName = mysqli_query($conn, "SELECT crop_name FROM crop_config WHERE id = $crop_id");
        $cropData = mysqli_fetch_assoc($getCropName);
        $cropName = $cropData['crop_name'] ?? 'unknown';

        $historyStmt = mysqli_prepare($conn, "INSERT INTO market_price_history (crop_name, price_per_kg) VALUES (?, ?)");
        mysqli_stmt_bind_param($historyStmt, "sd", $cropName, $new_price);
        mysqli_stmt_execute($historyStmt);

        $admin_id = $_SESSION['user_id'];
        $logMsg = "Admin updated $cropName price to $new_price MWK/kg";
        $logStmt = mysqli_prepare($conn, "INSERT INTO logs (message, user_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($logStmt, "si", $logMsg, $admin_id);
        mysqli_stmt_execute($logStmt);

        echo json_encode(['success' => true, 'message' => 'Price updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update price']);
    }
    exit;
}

if ($action === 'bulk_update') {
    $data = json_decode(file_get_contents('php://input'), true);
    $prices = $data['prices'] ?? [];

    if (empty($prices)) {
        echo json_encode(['success' => false, 'error' => 'No prices provided']);
        exit;
    }

    $updated = 0;
    $admin_id = $_SESSION['user_id'];

    foreach ($prices as $priceData) {
        $crop_id = intval($priceData['id'] ?? 0);
        $new_price = floatval($priceData['price'] ?? 0);

        if ($crop_id > 0 && $new_price >= 0) {
            $stmt = mysqli_prepare($conn, "UPDATE crop_config SET current_price = ?, price_updated_at = NOW() WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "di", $new_price, $crop_id);

            if (mysqli_stmt_execute($stmt)) {
                $getCropName = mysqli_query($conn, "SELECT crop_name FROM crop_config WHERE id = $crop_id");
                $cropData = mysqli_fetch_assoc($getCropName);
                $cropName = $cropData['crop_name'] ?? 'unknown';

                $historyStmt = mysqli_prepare($conn, "INSERT INTO market_price_history (crop_name, price_per_kg) VALUES (?, ?)");
                mysqli_stmt_bind_param($historyStmt, "sd", $cropName, $new_price);
                mysqli_stmt_execute($historyStmt);

                $updated++;
            }
        }
    }

    $logMsg = "Admin bulk updated $updated market prices";
    $logStmt = mysqli_prepare($conn, "INSERT INTO logs (message, user_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($logStmt, "si", $logMsg, $admin_id);
    mysqli_stmt_execute($logStmt);

    echo json_encode(['success' => true, 'message' => "$updated prices updated successfully", 'count' => $updated]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
?>
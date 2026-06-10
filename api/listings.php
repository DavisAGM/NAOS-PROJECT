<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? $action;
}

// ── ADD LISTING ──────────────────────────────────────────────────────────────
if ($action === 'add' && getUserRole() === 'farmer') {
    $user_id = $_SESSION['user_id'];

    if (!canUserSell($conn, $user_id)) {
        echo json_encode([
            'success'               => false,
            'error'                 => 'Active subscription required',
            'subscription_required' => true,
            'message'               => 'You need an active farmer subscription to list produce. Please subscribe to continue.'
        ]);
        exit;
    }

    $type        = trim($_POST['type']     ?? '');
    $quantity    = floatval($_POST['quantity'] ?? 0);
    $price       = floatval($_POST['price']    ?? 0);
    $description = trim($_POST['description'] ?? '');
    $unit        = in_array($_POST['unit'] ?? '', ['kg','bags','crates','tonnes']) ? $_POST['unit'] : 'kg';

    if (empty($type) || $quantity <= 0 || $price <= 0) {
        echo json_encode(['success' => false, 'error' => 'Valid produce type, quantity, and price are required.']);
        exit;
    }

    // Image upload — required
    if (empty($_FILES['produce_image']['name'])) {
        echo json_encode(['success' => false, 'error' => 'A photo of the produce is required.']);
        exit;
    }

    $file      = $_FILES['produce_image'];
    $allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    $maxSize   = 5 * 1024 * 1024; // 5 MB

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        echo json_encode(['success' => false, 'error' => 'Only JPG, PNG, or WebP images are allowed.']);
        exit;
    }
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'error' => 'Image must be under 5 MB.']);
        exit;
    }

    $uploadDir = '../assets/images/produce/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'produce_' . $user_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        echo json_encode(['success' => false, 'error' => 'Failed to save image. Please try again.']);
        exit;
    }

    $image_path = 'assets/images/produce/' . $filename;

    // Validate crop
    $stmt_crop = mysqli_prepare($conn, "SELECT id FROM crop_config WHERE LOWER(crop_name) = LOWER(?)");
    mysqli_stmt_bind_param($stmt_crop, "s", $type);
    mysqli_stmt_execute($stmt_crop);
    $crop_row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_crop));

    if (!$crop_row) {
        echo json_encode(['success' => false, 'error' => 'Invalid crop type. Please select a valid crop.']);
        exit;
    }
    $crop_id = $crop_row['id'];

    // Insert listing
    $status_val = 'available';
    $stmt = mysqli_prepare($conn,
        "INSERT INTO produce_listings (user_id, crop_id, produce_type, quantity, price, produce_image, description, unit, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "iisddssss",
        $user_id, $crop_id, $type, $quantity, $price, $image_path, $description, $unit, $status_val
    );

    $ok = @mysqli_stmt_execute($stmt);
    if (!$ok) {
        $stmt2 = mysqli_prepare($conn,
            "INSERT INTO produce_listings (user_id, crop_id, produce_type, quantity, price, produce_image, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt2, "iisddss",
            $user_id, $crop_id, $type, $quantity, $price, $image_path, $status_val
        );
        $ok = mysqli_stmt_execute($stmt2);
    }

    if (!$ok) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . mysqli_error($conn)]);
        exit;
    }

    $listing_id = mysqli_insert_id($conn);

    // ── Location-based buyer notifications ────────────────────────────────────
    // Get farmer's location
    $fStmt = mysqli_prepare($conn, "SELECT username, location FROM users WHERE id = ?");
    mysqli_stmt_bind_param($fStmt, "i", $user_id);
    mysqli_stmt_execute($fStmt);
    $farmer = mysqli_fetch_assoc(mysqli_stmt_get_result($fStmt));
    $farmer_location = trim($farmer['location'] ?? '');
    $farmer_name     = $farmer['username'] ?? 'A farmer';

    if (!empty($farmer_location)) {
        // Notify buyers in the same location (role column)
        $bStmt = mysqli_prepare($conn,
            "SELECT id FROM users
             WHERE role = 'buyer'
               AND is_active = 1
               AND id != ?
               AND LOWER(location) LIKE LOWER(?)"
        );
        $locPattern = '%' . $farmer_location . '%';
        mysqli_stmt_bind_param($bStmt, "is", $user_id, $locPattern);
        mysqli_stmt_execute($bStmt);
        $bResult = mysqli_stmt_get_result($bStmt);

        $notif_msg = "🌾 New produce near you: {$farmer_name} is selling " .
                     number_format($quantity, 0) . " {$unit} of " . ucfirst($type) .
                     " at MWK " . number_format($price, 0) . "/{$unit} in {$farmer_location}.";

        $nStmt = mysqli_prepare($conn,
            "INSERT INTO notifications (user_id, type, message, action_data, is_read)
             VALUES (?, 'new_listing', ?, ?, 0)"
        );
        $action_data = json_encode(['listing_id' => $listing_id, 'section' => 'listings']);

        while ($buyer = mysqli_fetch_assoc($bResult)) {
            mysqli_stmt_bind_param($nStmt, "iss", $buyer['id'], $notif_msg, $action_data);
            mysqli_stmt_execute($nStmt);
        }
    }

    echo json_encode(['success' => true, 'listing_id' => $listing_id]);

// ── GET LISTINGS ─────────────────────────────────────────────────────────────
} elseif ($action === 'get') {
    $user_only = isset($_GET['user_only']) || isset($_POST['user_only']);
    $search    = trim($_GET['search'] ?? '');
    $location  = trim($_GET['location'] ?? '');

    $query = "SELECT pl.id, pl.produce_type, pl.quantity, pl.price, pl.status,
                     pl.produce_image, pl.created_at,
                     COALESCE(pl.description,'') AS description,
                     COALESCE(pl.unit,'kg')       AS unit,
                     u.username, u.location
              FROM produce_listings pl
              JOIN users u ON pl.user_id = u.id";

    $conditions = [];
    $params     = [];
    $types      = '';

    if ($user_only) {
        $uid = $_SESSION['user_id'];
        $conditions[] = "pl.user_id = ?";
        $params[]     = $uid;
        $types       .= 'i';
    } else {
        $conditions[] = "pl.status = 'available'";
    }

    if (!empty($search)) {
        $conditions[] = "pl.produce_type LIKE ?";
        $params[]     = '%' . $search . '%';
        $types       .= 's';
    }
    if (!empty($location)) {
        $conditions[] = "u.location LIKE ?";
        $params[]     = '%' . $location . '%';
        $types       .= 's';
    }

    if ($conditions) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    $query .= " ORDER BY pl.id DESC LIMIT 100";

    if ($params) {
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($conn, $query);
    }

    $listings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $listings[] = $row;
    }
    echo json_encode(['listings' => $listings]);

// ── DELETE LISTING ────────────────────────────────────────────────────────────
} elseif ($action === 'delete' && getUserRole() === 'farmer') {
    $id      = intval($_POST['id'] ?? 0);
    $user_id = $_SESSION['user_id'];

    // Fetch image to delete
    $imgStmt = mysqli_prepare($conn, "SELECT produce_image FROM produce_listings WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($imgStmt, "ii", $id, $user_id);
    mysqli_stmt_execute($imgStmt);
    $imgRow = mysqli_fetch_assoc(mysqli_stmt_get_result($imgStmt));
    if ($imgRow && $imgRow['produce_image']) {
        $imgFile = '../' . $imgRow['produce_image'];
        if (file_exists($imgFile)) @unlink($imgFile);
    }

    mysqli_query($conn, "DELETE FROM produce_listings WHERE id = $id AND user_id = $user_id");
    echo json_encode(['success' => true]);

} else {
    echo json_encode(['error' => 'Invalid action or insufficient permissions']);
}
?>
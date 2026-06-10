<?php
header('Content-Type: application/json');
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'users') {
    $result = mysqli_query($conn, "SELECT id, username, role, location, lang, is_active FROM users");
    if ($result) {
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode(['users' => $users]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
} elseif ($action === 'reports') {
    $type = $_GET['type'] ?? 'logs';
    $period = $_GET['period'] ?? 'all';
    $startDate = $_GET['start_date'] ?? null;
    $endDate = $_GET['end_date'] ?? null;

    // Date Filter Logic
    $dateCondition = "";
    if ($period === 'daily') {
        $dateCondition = "AND JSON_DATE_COL >= CURDATE()";
    } elseif ($period === 'weekly') {
        $dateCondition = "AND JSON_DATE_COL >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
    } elseif ($period === 'monthly') {
        $dateCondition = "AND JSON_DATE_COL >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
    } elseif ($period === 'custom' && $startDate && $endDate) {
        $dateCondition = "AND JSON_DATE_COL BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
    }

    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
    $offset = ($page - 1) * $limit;

    $query = "";
    $countQuery = "";
    if ($type === 'users') {
        $col = "created_at";
        $where = str_replace("JSON_DATE_COL", $col, $dateCondition);
        $where = $where ? "WHERE 1=1 $where" : "";
        $query = "SELECT id, username, role, location, created_at as date FROM users $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $countQuery = "SELECT COUNT(*) as total FROM users $where";

    } elseif ($type === 'payments') {
        $col = "created_at";
        $where = str_replace("JSON_DATE_COL", "p." . $col, $dateCondition);
        $where = $where ? "WHERE 1=1 $where" : "";
        $query = "SELECT p.id, p.transaction_id, p.order_id, o.user_id, p.amount, p.currency, p.status, p.created_at as date 
                  FROM payments p
                  LEFT JOIN orders o ON p.order_id = o.id
                  $where ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";
        $countQuery = "SELECT COUNT(*) as total FROM payments p LEFT JOIN orders o ON p.order_id = o.id $where";

    } else { // logs
        $col = "timestamp";
        $where = str_replace("JSON_DATE_COL", $col, $dateCondition);
        $where = $where ? "WHERE 1=1 $where" : "";
        $query = "SELECT id, message, timestamp as date FROM logs $where ORDER BY timestamp DESC LIMIT $limit OFFSET $offset";
        $countQuery = "SELECT COUNT(*) as total FROM logs $where";
    }

    $result = mysqli_query($conn, $query);
    $countResult = mysqli_query($conn, $countQuery);

    if ($result && $countResult) {
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $totalCount = mysqli_fetch_assoc($countResult)['total'];
        echo json_encode([
            'reports' => $data,
            'total_count' => intval($totalCount),
            'page' => $page,
            'limit' => $limit
        ]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
} elseif ($action === 'stats') {
    $stats = [];
    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
    $stats['total_users'] = mysqli_fetch_assoc($res)['count'];
    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'farmer'");
    $stats['total_farmers'] = mysqli_fetch_assoc($res)['count'];
    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'buyer'");
    $stats['total_buyers'] = mysqli_fetch_assoc($res)['count'];
    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE payment_status = 'paid'");
    $stats['total_payments'] = ($res) ? mysqli_fetch_assoc($res)['count'] : 0;
    $actRes = mysqli_query($conn, "SELECT message, timestamp as date FROM logs ORDER BY timestamp DESC LIMIT 5");
    $stats['recent_activity'] = ($actRes) ? mysqli_fetch_all($actRes, MYSQLI_ASSOC) : [];
    echo json_encode($stats);

} elseif ($action === 'analytics') {
    $analytics = [];

    // User role distribution
    $res = mysqli_query($conn, "SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $analytics['role_distribution'] = mysqli_fetch_all($res, MYSQLI_ASSOC);

    // User status distribution
    $res = mysqli_query($conn, "SELECT is_active, COUNT(*) as count FROM users GROUP BY is_active");
    $analytics['status_distribution'] = mysqli_fetch_all($res, MYSQLI_ASSOC);

    // Growth over last 7 days (count)
    $res = mysqli_query($conn, "SELECT DATE(created_at) as date, COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY date ASC");
    $analytics['user_growth'] = mysqli_fetch_all($res, MYSQLI_ASSOC);

    // Revenue Trends (Last 30 days)
    $res = mysqli_query($conn, "SELECT DATE(created_at) as date, SUM(amount) as total FROM payments WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(created_at) ORDER BY date ASC");
    $revenue_trends = mysqli_fetch_all($res, MYSQLI_ASSOC);
    // Convert total to float for proper JSON serialization
    foreach ($revenue_trends as &$trend) {
        $trend['total'] = (float)$trend['total'];
    }
    $analytics['revenue_trends'] = $revenue_trends;

    // Top Crops in Listings
    $res = mysqli_query($conn, "SELECT cc.crop_name as name, COUNT(*) as count 
                               FROM produce_listings pl 
                               JOIN crop_config cc ON pl.crop_id = cc.id 
                               GROUP BY pl.crop_id 
                               ORDER BY count DESC LIMIT 5");
    $top_crops = mysqli_fetch_all($res, MYSQLI_ASSOC);
    // Convert count to int
    foreach ($top_crops as &$crop) {
        $crop['count'] = (int)$crop['count'];
    }
    $analytics['top_crops'] = $top_crops;

    // Regional User Distribution
    $res = mysqli_query($conn, "SELECT location as district, COUNT(*) as count 
                               FROM users 
                               WHERE location IS NOT NULL AND location != '' 
                               GROUP BY location 
                               ORDER BY count DESC LIMIT 10");
    $regional_data = mysqli_fetch_all($res, MYSQLI_ASSOC);
    // Convert count to int
    foreach ($regional_data as &$region) {
        $region['count'] = (int)$region['count'];
    }
    $analytics['regional_data'] = $regional_data;

    // Payment status distribution
    $res = mysqli_query($conn, "SELECT status as payment_status, COUNT(*) as count FROM payments GROUP BY status");
    $payment_stats = mysqli_fetch_all($res, MYSQLI_ASSOC);
    // Convert count to int
    foreach ($payment_stats as &$stat) {
        $stat['count'] = (int)$stat['count'];
    }
    $analytics['payment_stats'] = $payment_stats;

    echo json_encode($analytics);

} elseif ($action === 'get_settings') {
    $result = mysqli_query($conn, "SELECT setting_key, setting_value FROM system_settings");
    $settings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    echo json_encode(['success' => true, 'settings' => $settings]);

} elseif ($action === 'update_settings') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    $settings = $_POST['settings'] ?? []; // Map of key => value
    $success_count = 0;

    foreach ($settings as $key => $value) {
        // Skip system_name if it's sent (should be read-only)
        if ($key === 'system_name')
            continue;

        $stmt = mysqli_prepare($conn, "UPDATE system_settings SET setting_value = ?, updated_by = ? WHERE setting_key = ?");
        $updaterId = $_SESSION['user_id'] ?? null;
        mysqli_stmt_bind_param($stmt, "sis", $value, $updaterId, $key);
        if (mysqli_stmt_execute($stmt)) {
            $success_count++;
        }
        mysqli_stmt_close($stmt);
    }

    echo json_encode(['success' => true, 'message' => "$success_count settings updated"]);

} elseif ($action === 'payments') {
    $query = "SELECT p.id, p.transaction_id, p.order_id, p.amount, p.currency, p.status, p.created_at as date, o.user_id
              FROM payments p
              LEFT JOIN orders o ON p.order_id = o.id
              ORDER BY p.created_at DESC LIMIT 100";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $payments = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode(['payments' => $payments]);
    } else {
        echo json_encode(['payments' => []]);
    }
} else {
    echo json_encode(['error' => 'Invalid action']);
}
?>
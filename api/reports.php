<?php
header('Content-Type: application/json');
require '../includes/db.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = getUserRole(); // 'buyer' or 'farmer' (or 'admin' but they use admin/api.php)
$type = $_GET['type'] ?? '';
$period = $_GET['period'] ?? 'all';

// Mapping of Malawi districts/cities to coordinates
$malawi_locations = [
    'lilongwe' => '-13.9626,34.3015',
    'Lilongwe' => '-13.9626,34.3015',
    'blantyre' => '-15.7942,34.9159',
    'Blantyre' => '-15.7942,34.9159',
    'mzuzu' => '-11.4667,34.3667',
    'Mzuzu' => '-11.4667,34.3667',
    'zomba' => '-15.3833,35.3167',
    'Zomba' => '-15.3833,35.3167',
    'kasungu' => '-12.8333,33.4667',
    'Kasungu' => '-12.8333,33.4667',
    'rumphi' => '-10.7667,33.95',
    'Rumphi' => '-10.7667,33.95',
    'mzimba' => '-11.6333,33.6667',
    'Mzimba' => '-11.6333,33.6667',
    'salima' => '-13.7667,34.3',
    'Salima' => '-13.7667,34.3',
    'thyolo' => '-16.3167,34.95',
    'Thyolo' => '-16.3167,34.95',
    'mulanje' => '-16.4167,35.3667',
    'Mulanje' => '-16.4167,35.3667',
    'karonga' => '-9.9667,34.3',
    'Karonga' => '-9.9667,34.3',
    'chitipa' => '-9.7167,33.2',
    'Chitipa' => '-9.7167,33.2',
    'nsanje' => '-16.85,34.6667',
    'Nsanje' => '-16.85,34.6667',
    'mangochi' => '-15.4,34.6833',
    'Mangochi' => '-15.4,34.6833',
    'machinga' => '-15.6667,35.3',
    'Machinga' => '-15.6667,35.3',
    'nkhata bay' => '-11.6,34.2667',
    'Nkhata Bay' => '-11.6,34.2667',
];

// Helper function to convert location to coordinates
function getCoordinates($location) {
    global $malawi_locations;
    
    // If already in coordinate format (contains comma and is numeric)
    if (strpos($location, ',') !== false) {
        $parts = explode(',', $location);
        if (count($parts) === 2 && is_numeric(trim($parts[0])) && is_numeric(trim($parts[1]))) {
            return [trim($parts[0]), trim($parts[1])];
        }
    }
    
    // Try to find in city mapping
    if (isset($malawi_locations[$location])) {
        $coords = $malawi_locations[$location];
        $parts = explode(',', $coords);
        return [trim($parts[0]), trim($parts[1])];
    }
    
    // Default to Lilongwe if not found
    return [-13.9626, 34.3015];
}

// Helper for date condition
function getDateCondition($period, $col)
{
    $today = date('Y-m-d');
    if ($period === 'weekly') {
        return "AND $col >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND $col <= NOW()";
    } elseif ($period === 'monthly') {
        return "AND $col >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND $col <= NOW()";
    } elseif ($period === 'custom') {
        $start = $_GET['start_date'] ?? '';
        $end = $_GET['end_date'] ?? '';
        // Clamp end date to today — never allow future dates
        if ($end > $today) $end = $today;
        if ($start > $today) $start = $today;
        if ($start && $end) {
            return "AND $col BETWEEN '" . mysqli_real_escape_string($GLOBALS['conn'], $start) . " 00:00:00' AND '" . mysqli_real_escape_string($GLOBALS['conn'], $end) . " 23:59:59'";
        }
    }
    // Default to last 30 days if no valid period or custom range
    return "AND $col >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND $col <= NOW()";
}

$data = [];
$error = null;

try {
    if ($role === 'buyer') {
        if ($type === 'orders') {
            $dateCond = getDateCondition($period, "created_at");
            $query = "SELECT id as order_id, type as item, quantity, price, (quantity * price) as total, status, created_at as date 
                      FROM orders 
                      WHERE user_id = ? $dateCond 
                      ORDER BY created_at DESC";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_all($res, MYSQLI_ASSOC);

        } elseif ($type === 'market') {
            // Market History (using current prices as snapshot for MVP if history table is empty)
            // Market History (using current prices from crop_config as snapshot)
            $query = "SELECT crop_name, current_price as current_price, price_updated_at as last_updated 
                      FROM crop_config 
                      ORDER BY crop_name ASC";
            $res = mysqli_query($conn, $query);
            $data = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } elseif ($type === 'payments') {
            $dateCond = getDateCondition($period, "p.created_at");
            $query = "SELECT p.transaction_id, p.amount, p.currency, p.status, p.created_at as date, o.type as order_type 
                      FROM payments p
                      JOIN orders o ON p.order_id = o.id
                      WHERE o.user_id = ? $dateCond 
                      ORDER BY p.created_at DESC";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_all($res, MYSQLI_ASSOC);
        }

    } elseif ($role === 'farmer') {
        if ($type === 'sales') {
            $dateCond = getDateCondition($period, "o.created_at");
            $query = "SELECT o.id as order_id, p.produce_type as item, o.quantity, p.price as price, (o.quantity * p.price) as revenue, o.created_at as date 
                      FROM orders o 
                      JOIN produce_listings p ON o.listing_id = p.id 
                      WHERE p.user_id = ? $dateCond 
                      ORDER BY o.created_at DESC";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_all($res, MYSQLI_ASSOC);

        } elseif ($type === 'payments') {
            $dateCond = getDateCondition($period, "p.created_at");
            // For farmers: find payments for orders on their produce listings
            $query = "SELECT p.transaction_id, p.amount, p.currency, p.status, p.created_at as date, o.type as order_type 
                      FROM payments p
                      JOIN orders o ON p.order_id = o.id
                      JOIN produce_listings pl ON o.listing_id = pl.id
                      WHERE pl.user_id = ? $dateCond 
                      ORDER BY p.created_at DESC";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_all($res, MYSQLI_ASSOC);

        } elseif ($type === 'production') {
            $dateCond = getDateCondition($period, "date");
            $query = "SELECT date, CONCAT(activity_type, ' ', crop_name) as activity, quantity as yield, notes as input_usage 
                      FROM farm_activities 
                      WHERE user_id = ? $dateCond 
                      ORDER BY date DESC";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_all($res, MYSQLI_ASSOC);

        } elseif ($type === 'weather') {
            // Fetch location
            $stmt = mysqli_prepare($conn, "SELECT location FROM users WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $userRow = mysqli_fetch_assoc($res);
            $location = $userRow ? $userRow['location'] : 'Lilongwe';
            
            // Convert location (city name or coordinates) to coordinates
            list($lat, $lon) = getCoordinates($location);

            // Safety: Archive API usually only has data up to 3 days ago
            $max_end = date('Y-m-d', strtotime('-3 days'));

            // Determine dates based on period
            if ($period === 'weekly') {
                // Last 7 days ending 3 days ago
                $end_date = $max_end;
                $start_date = date('Y-m-d', strtotime('-9 days'));
            } elseif ($period === 'monthly') {
                // Last 30 days ending 3 days ago
                $end_date = $max_end;
                $start_date = date('Y-m-d', strtotime('-32 days'));
            } else {
                // Custom or All - use provided dates or defaults
                $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
                $end_date = $_GET['end_date'] ?? $max_end;
            }
            
            // Validate date range
            if ($end_date > $max_end) {
                $end_date = $max_end;
            }
            if ($start_date > $end_date) {
                $start_date = date('Y-m-d', strtotime('-30 days', strtotime($end_date)));
            }

            $weatherUrl = "https://archive-api.open-meteo.com/v1/archive?latitude=$lat&longitude=$lon&start_date=$start_date&end_date=$end_date&daily=temperature_2m_max,temperature_2m_min,precipitation_sum&timezone=Africa/Nairobi";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $weatherUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $response = curl_exec($ch);
            
            if ($response === false) {
                $err = curl_error($ch);
                curl_close($ch);
                throw new Exception("Weather API Connection Error: " . $err);
            }
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $wData = json_decode($response, true);
                $data = [];
                if (isset($wData['daily']) && isset($wData['daily']['time'])) {
                    foreach ($wData['daily']['time'] as $i => $date) {
                        $data[] = [
                            'date' => $date,
                            'max_temp' => $wData['daily']['temperature_2m_max'][$i] ?? null,
                            'min_temp' => $wData['daily']['temperature_2m_min'][$i] ?? null,
                            'rainfall' => $wData['daily']['precipitation_sum'][$i] ?? null
                        ];
                    }
                } else {
                    throw new Exception("Weather API Error: 'daily' data missing. Response: " . substr($response, 0, 200));
                }
                // Reverse to show newest first
                $data = array_reverse($data);
            } else {
                throw new Exception("Weather API Error: " . $http_code . " - " . substr($response, 0, 200));
            }
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

if ($error) {
    echo json_encode(['error' => $error]);
} else {
    echo json_encode(['reports' => $data]);
}
?>
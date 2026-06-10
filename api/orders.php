<?php
ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
require '../includes/db.php';
require '../includes/auth.php';


if (!$conn || !($conn instanceof mysqli)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

if (!isLoggedIn()) {
    ob_end_clean();
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? $_POST['action'] ?? 'add';

    if ($action === 'add') {

        if (getUserRole() !== 'buyer') {
            ob_end_clean();
            echo json_encode(['success' => false, 'error' => 'Buyer role required to place orders.']);
            exit;
        }

        $type = mysqli_real_escape_string($conn, (string) ($data['orderType'] ?? $_POST['orderType'] ?? ''));
        $quantity = floatval($data['orderQuantity'] ?? $_POST['orderQuantity'] ?? $data['quantity'] ?? $_POST['quantity'] ?? 0);
        $listing_id = intval($data['listing_id'] ?? $_POST['listing_id'] ?? 0);
        $contact = mysqli_real_escape_string($conn, (string) ($data['buyer_contact'] ?? $_POST['buyer_contact'] ?? ''));

        if ($quantity <= 0 || empty($contact)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'error' => 'Quantity must be positive and contact details are required.']);
            exit;
        }

        if ($listing_id) {

            $listing_sql = "SELECT user_id, produce_type, price FROM produce_listings WHERE id = $listing_id";
            $listing_query = mysqli_query($conn, $listing_sql);

            if (!$listing_query) {
                ob_end_clean();
                echo json_encode(['success' => false, 'error' => 'Error querying listing: ' . mysqli_error($conn)]);
                exit;
            }

            if ($listing = mysqli_fetch_assoc($listing_query)) {
                $farmer_id = $listing['user_id'];
                $produce_type = mysqli_real_escape_string($conn, $listing['produce_type']);
                $price = floatval($listing['price']);


                $insert_sql = "INSERT INTO orders (user_id, listing_id, `type`, quantity, price, buyer_contact, status, payment_status) VALUES ($user_id, $listing_id, '$produce_type', $quantity, $price, '$contact', 'pending', 'unpaid')";
                $res = mysqli_query($conn, $insert_sql);

                if (!$res) {
                    ob_end_clean();
                    echo json_encode(['success' => false, 'error' => 'Database error (specific): ' . mysqli_error($conn)]);
                    exit;
                }


                $buyer_name = function_exists('getUsername') ? getUsername($conn, $user_id) : 'Buyer';
                $msg = mysqli_real_escape_string($conn, "New order received from $buyer_name for $produce_type. Please check your incoming orders.");
                mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ($farmer_id, '$msg')");
            } else {
                ob_end_clean();
                echo json_encode(['success' => false, 'error' => 'Listing not found']);
                exit;
            }
        } else {
            // General order
            $res = mysqli_query($conn, "INSERT INTO orders (user_id, `type`, quantity, buyer_contact, status, payment_status) VALUES ($user_id, '$type', $quantity, '$contact', 'pending', 'unpaid')");

            if (!$res) {
                ob_end_clean();
                echo json_encode(['success' => false, 'error' => 'Database error (general): ' . mysqli_error($conn)]);
                exit;
            }
        }
        $order_id = mysqli_insert_id($conn);
        ob_end_clean();
        echo json_encode(['success' => true, 'order_id' => $order_id]);
    } elseif ($action === 'view') {
        $order_id = intval($data['order_id'] ?? $_POST['order_id'] ?? 0);
        if ($order_id) {
            $res = mysqli_query($conn, "SELECT o.*, u.username as buyer_name, l.produce_type, l.price as price_per_unit, u.phone_number as buyer_phone 
                                      FROM orders o 
                                      LEFT JOIN users u ON o.user_id = u.id 
                                      LEFT JOIN produce_listings l ON o.listing_id = l.id 
                                      WHERE o.id = $order_id");
            if ($order = mysqli_fetch_assoc($res)) {
                $buyer_id = intval($order['user_id']);
                if ($buyer_id > 0) {
                    mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ($buyer_id, 'A farmer is currently reviewing your order #$order_id.')");
                }
                ob_end_clean();
                echo json_encode(['success' => true, 'order' => $order]);
            } else {
                ob_end_clean();
                echo json_encode(['success' => false, 'error' => 'Order not found']);
            }
        }
    } elseif ($action === 'update_status') {
        $order_id = intval($data['order_id']);
        $new_status = mysqli_real_escape_string($conn, $data['status']);
        
        // Validate status transitions
        $allowed_statuses = ['pending', 'confirmed', 'delivered', 'cancelled'];
        if (!in_array($new_status, $allowed_statuses)) {
            ob_end_clean();
            echo json_encode(['error' => 'Invalid status']);
            exit;
        }

        // Check ownership
        $check_sql = "SELECT o.user_id as buyer_id, l.user_id as farmer_id, o.status as current_status 
                     FROM orders o 
                     LEFT JOIN produce_listings l ON o.listing_id = l.id 
                     WHERE o.id = $order_id";
        $check_res = mysqli_query($conn, $check_sql);
        $order_info = mysqli_fetch_assoc($check_res);

        if (!$order_info || ($order_info['buyer_id'] != $user_id && $order_info['farmer_id'] != $user_id)) {
            ob_end_clean();
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $update_sql = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
        if (mysqli_query($conn, $update_sql)) {
            // Notify other party
            $target_user = ($user_id == $order_info['buyer_id']) ? $order_info['farmer_id'] : $order_info['buyer_id'];
            $notifier = ($user_id == $order_info['buyer_id']) ? 'Buyer' : 'Farmer';
            $msg = "Order #$order_id status updated to $new_status by $notifier.";
            mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ($target_user, '$msg')");
            
            ob_end_clean();
            echo json_encode(['success' => true]);
        } else {
            ob_end_clean();
            echo json_encode(['error' => 'Update failed']);
        }
        exit;
    } elseif ($action === 'update_courier') {
        $order_id = intval($data['order_id']);
        $courier = mysqli_real_escape_string($conn, $data['courier']);
        $tracking = mysqli_real_escape_string($conn, $data['tracking']);
        
        // Check if user is the farmer for this order
        $check_sql = "SELECT l.user_id as farmer_id, o.user_id as buyer_id FROM orders o JOIN produce_listings l ON o.listing_id = l.id WHERE o.id = $order_id";
        $check_res = mysqli_query($conn, $check_sql);
        $order_info = mysqli_fetch_assoc($check_res);
        
        if (!$order_info || $order_info['farmer_id'] != $user_id) {
            ob_end_clean();
            echo json_encode(['error' => 'Unauthorized or invalid order']);
            exit;
        }
        
        $update_sql = "UPDATE orders SET courier_name = '$courier', tracking_number = '$tracking', shipment_date = NOW(), status = 'shipped' WHERE id = $order_id";
        if (mysqli_query($conn, $update_sql)) {
            $msg = "Your order #$order_id has been dispatched via $courier. Tracking: $tracking";
            mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ({$order_info['buyer_id']}, '$msg')");
            ob_end_clean();
            echo json_encode(['success' => true]);
        } else {
            ob_end_clean();
            echo json_encode(['error' => 'Failed to update courier details']);
        }
        exit;
    } elseif ($action === 'confirm_receipt') {
        $order_id = intval($data['order_id']);
        
        // Check if user is the buyer for this order
        $check_sql = "SELECT o.user_id as buyer_id, l.user_id as farmer_id, o.escrow_status, o.price, o.quantity 
                     FROM orders o 
                     LEFT JOIN produce_listings l ON o.listing_id = l.id 
                     WHERE o.id = $order_id";
        $check_res = mysqli_query($conn, $check_sql);
        $order_info = mysqli_fetch_assoc($check_res);
        
        if (!$order_info || $order_info['buyer_id'] != $user_id) {
            ob_end_clean();
            echo json_encode(['error' => 'Unauthorized or invalid order']);
            exit;
        }
        
        // Mark as released and delivered
        $update_sql = "UPDATE orders SET escrow_status = 'released', status = 'delivered' WHERE id = $order_id";
        if (mysqli_query($conn, $update_sql)) {
            $msg = "Buyer has confirmed receipt of order #$order_id. Funds have been released to your account.";
            mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ({$order_info['farmer_id']}, '$msg')");
            ob_end_clean();
            echo json_encode(['success' => true]);
        } else {
            ob_end_clean();
            echo json_encode(['error' => 'Failed to confirm receipt']);
        }
        exit;
    }
} else {

    $action = $_GET['action'] ?? 'get';

    if ($action === 'incoming' && getUserRole() === 'farmer') {

        $result = mysqli_query($conn, "SELECT o.id, l.produce_type, o.quantity, o.buyer_contact, u.username as buyer_name, o.created_at, o.status, o.escrow_status, o.courier_name, o.tracking_number 
                                     FROM orders o 
                                     JOIN produce_listings l ON o.listing_id = l.id 
                                     JOIN users u ON o.user_id = u.id 
                                     WHERE l.user_id = $user_id ORDER BY o.id DESC");
        $orders = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $orders[] = $row;
            }
        }
        ob_end_clean();
        echo json_encode(['orders' => $orders]);
    } else {

        // Auto-cleanup stale pending payments (older than 1 minute)
        mysqli_query($conn, "UPDATE payments SET status = 'failed' WHERE status = 'pending' AND created_at < DATE_SUB(NOW(), INTERVAL 1 MINUTE)");

        $result = mysqli_query($conn, "SELECT o.id, o.type, o.quantity, o.status, o.payment_status, l.produce_type, o.escrow_status, o.courier_name, o.tracking_number, 
                                     (SELECT COUNT(*) FROM payments p WHERE p.order_id = o.id AND p.status = 'pending') as pending_count
                                     FROM orders o 
                                     LEFT JOIN produce_listings l ON o.listing_id = l.id 
                                     WHERE o.user_id = $user_id ORDER BY o.id DESC");
        $orders = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['produce_type'])
                    $row['type'] = $row['produce_type'];
                $orders[] = $row;
            }
        }
        ob_end_clean();
        echo json_encode(['orders' => $orders]);
    }
}
?>
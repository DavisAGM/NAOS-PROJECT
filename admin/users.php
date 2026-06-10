<?php
header('Content-Type: application/json');
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'];

if ($action === 'list') {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 8;
    $offset = ($page - 1) * $limit;

    $where = "WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($search)) {
        $where .= " AND (username LIKE ? OR location LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ss";
    }

    // Count Query
    $countQuery = "SELECT COUNT(*) as total FROM users $where";
    $countStmt = mysqli_prepare($conn, $countQuery);
    if (!empty($params)) {
        mysqli_stmt_bind_param($countStmt, $types, ...$params);
    }
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    $totalCount = mysqli_fetch_assoc($countResult)['total'];
    mysqli_stmt_close($countStmt);

    // List Query
    $query = "SELECT id, username, role, location, lang, is_active FROM users $where ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $query);
    $types .= "ii";
    $params[] = $limit;
    $params[] = $offset;

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_stmt_close($stmt);

    echo json_encode([
        'users' => $users,
        'total_count' => intval($totalCount),
        'page' => $page,
        'limit' => $limit
    ]);

} elseif ($action === 'add') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone_number'] ?? '');
    $role = $_POST['role'] ?? 'farmer';
    $location = trim($_POST['location'] ?? '');
    $lang = $_POST['lang'] ?? 'en';

    // Validation
    if (empty($username) || empty($password) || empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'Username, password, and phone number are required']);
        exit;
    }

    if (!preg_match('/^0\d{9}$/', $phone)) {
        echo json_encode(['success' => false, 'error' => 'Phone number must be exactly 10 digits and start with 0']);
        exit;
    }

    if (!in_array($role, ['farmer', 'buyer'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid role. Admins cannot add other admins.']);
        exit;
    }

    // Check for duplicate username or phone
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR phone_number = ?");
    mysqli_stmt_bind_param($checkStmt, "ss", $username, $phone);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        echo json_encode(['success' => false, 'error' => 'That username or phone number is already taken.']);
        exit;
    }
    mysqli_stmt_close($checkStmt);

    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password_hash, role, location, lang, phone_number, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
    mysqli_stmt_bind_param($stmt, "ssssss", $username, $password_hash, $role, $location, $lang, $phone);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'We have added the new user.']);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);

} elseif ($action === 'edit') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $role = $_POST['role'] ?? '';
    $phone = trim($_POST['phone_number'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $lang = $_POST['lang'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($username) || empty($role) || empty($phone) || $user_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    if (!preg_match('/^0\d{9}$/', $phone)) {
        echo json_encode(['success' => false, 'error' => 'Phone number must be exactly 10 digits and start with 0']);
        exit;
    }

    if (!in_array($role, ['admin', 'farmer', 'buyer'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid role']);
        exit;
    }

    // Check for duplicate username or phone (excluding current user)
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE (username = ? OR phone_number = ?) AND id != ?");
    mysqli_stmt_bind_param($checkStmt, "ssi", $username, $phone, $user_id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        echo json_encode(['success' => false, 'error' => 'Username or phone number already exists']);
        exit;
    }
    mysqli_stmt_close($checkStmt);

    // Update user - with or without password
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, password_hash = ?, role = ?, location = ?, lang = ?, phone_number = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssssi", $username, $password_hash, $role, $location, $lang, $phone, $user_id);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, role = ?, location = ?, lang = ?, phone_number = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssssi", $username, $role, $location, $lang, $phone, $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);

} elseif ($action === 'deactivate') {
    $user_id = intval($_POST['user_id'] ?? 0);

    if ($user_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
        exit;
    }

    // Prevent deactivating yourself
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
        echo json_encode(['success' => false, 'error' => 'Cannot deactivate your own account']);
        exit;
    }

    $stmt = mysqli_prepare($conn, "UPDATE users SET is_active = 0 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'User deactivated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);

} elseif ($action === 'activate') {
    $user_id = intval($_POST['user_id'] ?? 0);

    if ($user_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
        exit;
    }

    $stmt = mysqli_prepare($conn, "UPDATE users SET is_active = 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'User activated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);

} elseif ($action === 'get') {
    $user_id = intval($_GET['user_id'] ?? 0);

    if ($user_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT id, username, phone_number, role, location, lang, is_active FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'user' => $row]);
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }
    mysqli_stmt_close($stmt);

} else {
    echo json_encode(['error' => 'Invalid action']);
}
?>
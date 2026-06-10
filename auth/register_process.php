<?php
header('Content-Type: application/json');
ob_start();
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

// Support both FormData (multipart) and JSON input
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';
$roles = $data['roles'] ?? [];
$gender = $data['gender'] ?? 'male';
$phone = trim($data['phone'] ?? '');
$location = trim($data['location'] ?? '');
$lang = $data['lang'] ?? 'en';

// Validate required fields
if (empty($username) || empty($password) || empty($phone)) {
    echo json_encode(['success' => false, 'error' => 'Username, password, and phone number are required']);
    exit();
}

if (strlen($username) < 8) {
    echo json_encode(['success' => false, 'error' => 'Username must be at least 8 characters long']);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters long']);
    exit();
}

if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    echo json_encode(['success' => false, 'error' => 'Password must contain both letters and numbers']);
    exit();
}

if (!preg_match('/^0\d{9}$/', $phone)) {
    echo json_encode(['success' => false, 'error' => 'Phone number must be exactly 10 digits and start with 0']);
    exit();
}

// Validate roles - must select at least one
if (empty($roles) || !is_array($roles)) {
    echo json_encode(['success' => false, 'error' => 'Please select at least one role (Farmer or Buyer)']);
    exit();
}

// Validate role values
$validRoles = ['farmer', 'buyer'];
foreach ($roles as $role) {
    if (!in_array($role, $validRoles)) {
        echo json_encode(['success' => false, 'error' => 'Invalid role selected']);
        exit();
    }
}

// National ID Upload Validation - ONLY required for Farmers
$nationalIdPath = null;
$ext = null;
$tempIdFile = null;

if (isset($_FILES['national_id']) && $_FILES['national_id']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['national_id'];

    // Validate file size (max 5 MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'National ID file must be under 5 MB']);
        exit();
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $detectedType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($detectedType, $allowedTypes)) {
        echo json_encode(['success' => false, 'error' => 'Invalid file type. Please upload a JPG, PNG, or PDF file.']);
        exit();
    }

    // Determine file extension
    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf',
    ];
    $ext = $extMap[$detectedType] ?? 'bin';
    $tempIdFile = $file['tmp_name'];
} elseif (in_array('farmer', $roles)) {
    // Required only if farmer role is present
    echo json_encode(['success' => false, 'error' => 'Please upload your National ID document']);
    exit();
}

// Check if id_verification_status column exists
$verColCheck = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'id_verification_status'");
$hasVerColumn = mysqli_num_rows($verColCheck) > 0;

if ($hasVerColumn) {
    // Delete any rejected users with this username or phone number so they can try again
    $delStmt = mysqli_prepare($conn, "DELETE FROM users WHERE (username = ? OR phone_number = ?) AND id_verification_status = 'rejected'");
    if ($delStmt) {
        mysqli_stmt_bind_param($delStmt, "ss", $username, $phone);
        mysqli_stmt_execute($delStmt);
        mysqli_stmt_close($delStmt);
    }
}

// Check if username or phone already exists
$checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR phone_number = ?");
mysqli_stmt_bind_param($checkStmt, "ss", $username, $phone);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);

if (mysqli_stmt_num_rows($checkStmt) > 0) {
    echo json_encode(['success' => false, 'error' => 'Username or Phone Number already taken']);
    exit();
}
mysqli_stmt_close($checkStmt);

// Generate Verification Code
$verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Insert new user with first role as primary
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$primaryRole = $roles[0]; // First selected role is primary

// Extract farmer_id if provided
$farmerId = trim($data['farmer_id'] ?? '');

// Status Logic:
$verificationStatus = 'approved';
$verificationNotes = '';
$isFarmer = in_array('farmer', $roles);

if ($isFarmer) {
    if (empty($farmerId)) {
        echo json_encode(['success' => false, 'error' => 'Farmer ID is required for farmers']);
        exit();
    }
    
    // Check against CSV
    $csvPath = __DIR__ . '/../malawian_farmers.csv';
    $isVerified = false;
    if (file_exists($csvPath) && ($handle = fopen($csvPath, "r")) !== FALSE) {
        $headers = fgetcsv($handle); // Skip header
        while (($row = fgetcsv($handle)) !== FALSE) {
            $csvFarmerId = $row[0] ?? '';
            $csvFullName = $row[3] ?? '';
            
            if (strcasecmp(trim($csvFarmerId), $farmerId) === 0 && strcasecmp(trim($csvFullName), $username) === 0) {
                $isVerified = true;
                break;
            }
        }
        fclose($handle);
    }
    
    if ($isVerified) {
        $verificationStatus = 'approved';
        $verificationNotes = 'Automatically verified against Ministry of Agriculture records.';
    } else {
        // Stop registration if verification fails
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Farmer identity verification failed. Your Farmer ID or Full Name does not match our records. Please try again.']);
        exit();
    }
}

// Check if active_role column exists (migrations run)
$columnCheck = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'active_role'");
$hasActiveRoleColumn = mysqli_num_rows($columnCheck) > 0;

if ($hasActiveRoleColumn && $hasVerColumn) {
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password_hash, role, gender, phone_number, phone_verification_code, location, lang, is_phone_verified, is_profile_complete, active_role, national_id_path, id_verification_status, id_verification_notes, farmer_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?)");
    // national_id_path is empty placeholder; will update after move
    $tempPath = '';
    mysqli_stmt_bind_param($stmt, "sssssssssssss", $username, $hashedPassword, $primaryRole, $gender, $phone, $verificationCode, $location, $lang, $primaryRole, $tempPath, $verificationStatus, $verificationNotes, $farmerId);
} elseif ($hasActiveRoleColumn) {
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password_hash, role, gender, phone_number, phone_verification_code, location, lang, is_phone_verified, is_profile_complete, active_role, farmer_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssssssss", $username, $hashedPassword, $primaryRole, $gender, $phone, $verificationCode, $location, $lang, $primaryRole, $farmerId);
} else {
    // Fallback for pre-migration database
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password_hash, role, gender, phone_number, phone_verification_code, location, lang, is_phone_verified, is_profile_complete, farmer_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?)");
    mysqli_stmt_bind_param($stmt, "sssssssss", $username, $hashedPassword, $primaryRole, $gender, $phone, $verificationCode, $location, $lang, $farmerId);
}

if (mysqli_stmt_execute($stmt)) {
    $userId = mysqli_insert_id($conn);

    // Move uploaded National ID file (if provided)
    if ($tempIdFile) {
        $uploadDir = __DIR__ . '/../assets/uploads/national_ids/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $savedFilename = "id_{$userId}_" . time() . ".{$ext}";
        $savedPath = $uploadDir . $savedFilename;

        if (move_uploaded_file($tempIdFile, $savedPath)) {
            $relativePath = "assets/uploads/national_ids/{$savedFilename}";
            if ($hasVerColumn) {
                $upd = mysqli_prepare($conn, "UPDATE users SET national_id_path = ? WHERE id = ?");
                mysqli_stmt_bind_param($upd, "si", $relativePath, $userId);
                mysqli_stmt_execute($upd);
                mysqli_stmt_close($upd);
            }
        }
    }

    // Check if user_roles table exists (migrations run)
    $tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'user_roles'");
    $hasUserRolesTable = mysqli_num_rows($tableCheck) > 0;

    if ($hasUserRolesTable && function_exists('assignRole')) {
        // Insert all selected roles into user_roles table
        foreach ($roles as $index => $role) {
            $isPrimary = ($index === 0) ? 1 : 0; // First role is primary
            assignRole($conn, $userId, $role, $isPrimary);
        }

        // Create subscription entries for each role
        foreach ($roles as $role) {
            $subStmt = mysqli_prepare($conn, "INSERT INTO subscriptions (user_id, role_type, status) VALUES (?, ?, 'inactive')");
            mysqli_stmt_bind_param($subStmt, "is", $userId, $role);
            mysqli_stmt_execute($subStmt);
            mysqli_stmt_close($subStmt);
        }
    } else {
        // Fallback: Create single subscription entry (pre-migration)
        $subStmt = mysqli_prepare($conn, "INSERT INTO subscriptions (user_id, status) VALUES (?, 'inactive')");
        mysqli_stmt_bind_param($subStmt, "i", $userId);
        mysqli_stmt_execute($subStmt);
        mysqli_stmt_close($subStmt);
    }

    // Send SMS
    require_once __DIR__ . '/../includes/sms_helper.php';
    sendVerificationSMS($phone, $verificationCode);


    ob_clean();
    echo json_encode([
        'success' => true,
        'redirect' => 'verify_phone.php',
        'phone' => $phone,
        'roles' => $roles
    ]);
} else {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Registration failed: ' . mysqli_error($conn)]);
}
?>
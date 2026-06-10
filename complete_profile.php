<?php
require 'includes/db.php';
require 'includes/auth.php'; // handles session startup

if (!isLoggedIn()) {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $newName = "profile_" . $user_id . "_" . time() . "." . $ext;
            $uploadDir = __DIR__ . '/assets/images/profiles/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                $stmt = mysqli_prepare($conn, "UPDATE users SET profile_picture = ?, is_profile_complete = 1 WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $newName, $user_id);
                mysqli_stmt_execute($stmt);

                $role = $_SESSION['role'];
                $redirect = ($role === 'buyer') ? 'buyer/dashboard.php' : 'farmer/dashboard.php';
                if ($role === 'admin') $redirect = 'admin/dashboard.php';

                header("Location: $redirect");
                exit();
            }
        }
    }
    $error = "Please select a valid image (JPG, PNG, GIF).";
}

// Handle Skip
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['skip'])) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET is_profile_complete = 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $role = $_SESSION['role'];
    $redirect = ($role === 'buyer') ? 'buyer/dashboard.php' : 'farmer/dashboard.php';
    if ($role === 'admin') $redirect = 'admin/dashboard.php';

    header("Location: $redirect");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Profile - NAOS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f3f4f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; font-family: sans-serif; }
        .card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .avatar-box { margin-bottom: 1.5rem; display: flex; justify-content: center; }
        .error { color: red; font-size: 0.85rem; margin-bottom: 1rem; }
        input[type="file"] { margin: 1rem 0; font-size: 0.9rem; }
        .btn-main { width: 100%; padding: 0.8rem; background: #1b4d3e; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .btn-skip { background: none; border: none; color: #666; margin-top: 1rem; cursor: pointer; font-size: 0.9rem; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="color:#1b4d3e; margin-top:0;">Complete Profile</h2>
        <p style="color:#666; font-size: 0.9rem;">Add a photo or skip to use your initials.</p>

        <div class="avatar-box">
             <?php echo renderAvatar($conn, $user_id, 'assets/images/profiles/', 'avatarPreview', '80px'); ?>
        </div>

        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="profile_pic" accept="image/*" required>
            <button type="submit" class="btn-main">Upload & Continue</button>
        </form>

        <form method="POST">
            <button type="submit" name="skip" value="1" class="btn-skip">Skip for now</button>
        </form>
    </div>
</body>
</html>
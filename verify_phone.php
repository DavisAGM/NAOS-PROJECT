<?php
require 'includes/db.php';
require 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    $code = $_POST['code'] ?? '';

    // 1. Verify Code
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE phone_number = ? AND phone_verification_code = ?");
    mysqli_stmt_bind_param($stmt, "ss", $phone, $code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Success! Update user status
        mysqli_query($conn, "UPDATE users SET is_phone_verified = 1, phone_verification_code = NULL WHERE id = " . $user['id']);
        
        // Check if the user is a farmer and require SMS helper
        require_once 'includes/sms_helper.php';
        
        $res = mysqli_query($conn, "SELECT role, id_verification_status, id_verification_notes FROM users WHERE id = " . $user['id']);
        $userData = mysqli_fetch_assoc($res);
        $roles = explode(',', $userData['role'] ?? '');
        $status = $userData['id_verification_status'] ?? 'pending';
        
        if (in_array('farmer', $roles)) {
            $smsMessage = ($status === 'approved') 
                ? "NAOS: Your farmer identity has been successfully verified against the records from the Ministry of Agriculture. Welcome!" 
                : "NAOS: Farmer identity verification failed. Reason: " . ($userData['id_verification_notes'] ?? 'Mismatch with records') . ". Please check your Farmer ID and Full Name.";
            send_sms($phone, $smsMessage);
        }
        
        if ($status === 'approved') {
            $success = "Phone verified successfully! You can login now. Thank you for joining NAOS.";
        } else {
            $success = "Phone verified successfully! You can login after admin's review of your ID. Thank you for registering with NAOS.";
        }
    } else {
        $error = "Invalid verification code. Please try again.";
    }
}

$phoneDisplay = $_GET['phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Phone - NAOS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .verify-container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .success-msg {
            color: green;
            background: #e8f5e9;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .error-msg {
            color: red;
            background: #ffebee;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>

<body style="background-color: #f3f4f6;">
    <div class="verify-container">
        <h2 style="text-align: center; color: #1b4d3e; margin-bottom: 1.5rem;">Verify Your Phone</h2>

        <?php if ($success): ?>
            <div class="success-msg">
                <?php echo $success; ?>
                <br><br>
                <a href="auth.php" class="btn"
                    style="display:inline-block; text-decoration:none; background:#1b4d3e; color:white; padding:0.5rem 1rem; border-radius:4px;">Go
                    to Login</a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="error-msg">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <p style="text-align: center; margin-bottom: 1.5rem; color: #666;">
                We sent a 6-digit code to <strong>
                    <?php echo htmlspecialchars($phoneDisplay); ?>
                </strong>.
                <!-- <br><em>(Ngati sinamupeze, check the txt file AGM)</em> -->
            </p>

            <form method="POST">
                <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phoneDisplay); ?>">

                <div style="margin-bottom: 1rem;">
                    <label for="code" style="display:block; margin-bottom:0.5rem; font-weight:bold;">Verification
                        Code</label>
                    <input type="text" id="code" name="code" placeholder="123456" maxlength="6" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1.2rem; letter-spacing: 2px; text-align: center;">
                </div>

                <button type="submit"
                    style="width: 100%; padding: 0.75rem; background: #1b4d3e; color: white; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer;">Verify</button>

                <div style="margin-top: 1rem; text-align: center; font-size: 0.9rem;">
                    <button type="button" id="resendBtn" onclick="resendCode()"
                        style="background: none; border: none; color: #1b4d3e; text-decoration: underline; cursor: pointer;">
                        Resend Code
                    </button>
                    <span id="timerSpan" style="display: none; color: #666;">
                        Resend in <span id="timer">60</span>s
                    </span>
                    <div id="resendMsg" style="margin-top: 0.5rem; font-size: 0.85rem;"></div>
                </div>

                <script>
                    let timeLeft = 60;
                    let timerId = null;

                    function startTimer() {
                        const btn = document.getElementById('resendBtn');
                        const timerSpan = document.getElementById('timerSpan');
                        const timerDisplay = document.getElementById('timer');

                        btn.style.display = 'none';
                        timerSpan.style.display = 'inline';
                        timeLeft = 60;
                        timerDisplay.textContent = timeLeft;

                        timerId = setInterval(() => {
                            timeLeft--;
                            timerDisplay.textContent = timeLeft;
                            if (timeLeft <= 0) {
                                clearInterval(timerId);
                                btn.style.display = 'inline';
                                timerSpan.style.display = 'none';
                            }
                        }, 1000);
                    }

                    function resendCode() {
                        const phone = document.querySelector('input[name="phone"]').value;
                        const msgDiv = document.getElementById('resendMsg');

                        msgDiv.textContent = 'Sending...';
                        msgDiv.style.color = '#666';

                        fetch('/naos/auth/resend_code.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ phone: phone })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    msgDiv.textContent = 'Code resent successfully! Check your SMS.';
                                    msgDiv.style.color = 'green';
                                    startTimer();
                                } else {
                                    msgDiv.textContent = data.error || 'Failed to resend code.';
                                    msgDiv.style.color = 'red';
                                }
                            })
                            .catch(error => {
                                msgDiv.textContent = 'Network error. Try again.';
                                msgDiv.style.color = 'red';
                            });
                    }
                </script>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>
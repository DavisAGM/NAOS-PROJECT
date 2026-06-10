<?php
require 'includes/db.php';  // MySQLi connection
require 'includes/auth.php';
require 'includes/lang.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1b4d3e">
    <title>Nyasa Agricultural Optimization System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/toast.css">
    <link rel="stylesheet" href="assets/vendor/css/all.min.css">
</head>

<body class="auth-page">
    <div class="container">
        <div class="header">
            <div class="header-content">
                <img src="assets/images/NAOS LOGO.png" alt="NAOS Logo">
                <p><?php echo t('naos_desc'); ?></p>
            </div>
        </div>

        <div class="form-container">
            <!-- Error Message -->
            <div id="errorMsg" class="error"></div>

            <!-- Login Form -->
            <div id="loginSection">
                <h2 class="text-center" style="margin-bottom: 1.5rem; color: var(--primary-color);">
                    <!-- <?php echo t('welcome_back'); ?> -->
                </h2>
                <form id="loginForm">
                    <div class="mb-2">
                        <label for="loginUsername" class="visually-hidden"><?php echo t('username'); ?></label>
                        <input type="text" id="loginUsername" placeholder="<?php echo t('username'); ?>" required>
                    </div>
                    <div class="mb-2">
                        <label for="loginPassword" class="visually-hidden"><?php echo t('password'); ?></label>
                        <div class="password-input-wrapper">
                            <input type="password" id="loginPassword" placeholder="<?php echo t('password'); ?>"
                                required>
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('loginPassword', this)" aria-label="Toggle password visibility">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"><?php echo t('login'); ?></button>
                </form>
                <div class="text-center mt-4 text-sm">
                    Don't have an account? <a onclick="showRegister()">Register here</a>
                    <br><br>
                    <a onclick="showForgotPassword()"
                        style="color: var(--primary-color); cursor: pointer; text-decoration: underline;">Forgot
                        Password?</a>
                </div>
            </div>

            <!-- Forgot Password Section -->
            <div id="forgotPasswordSection" style="display:none;">
                <h2 class="text-center" style="margin-bottom: 1.5rem; color: var(--primary-color);">
                    Reset Your Password
                </h2>

                <!--  Enter Phone Number -->
                <div id="forgotStep1">
                    <p style="text-align: center; margin-bottom: 1.5rem; color: #666;">
                        Enter your phone number to receive a verification code
                    </p>
                    <form id="forgotPhoneForm">
                        <div class="mb-2">
                            <label for="forgotPhone" class="visually-hidden">Phone Number</label>
                            <input type="tel" id="forgotPhone" placeholder="Phone Number" required
                                style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px;">
                        </div>
                        <button type="submit">Send Verification Code</button>
                    </form>
                    <div class="text-center mt-4 text-sm">
                        <a onclick="showLogin()" style="cursor: pointer;">Back to Login</a>
                    </div>
                </div>

                <!-- Verify Code -->
                <div id="forgotStep2" style="display:none;">
                    <p style="text-align: center; margin-bottom: 1.5rem; color: #666;">
                        We sent a 6-digit code to <strong id="forgotPhoneDisplay"></strong>
                        <br><em>(Ngati sinamupeze, check the txt file AGM)</em>
                    </p>
                    <form id="forgotVerifyForm">
                        <div class="mb-2">
                            <label for="forgotCode" class="visually-hidden">Verification Code</label>
                            <input type="text" id="forgotCode" placeholder="123456" maxlength="6" required
                                style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1.2rem; letter-spacing: 2px; text-align: center;">
                        </div>
                        <button type="submit">Verify Code</button>

                        <div style="margin-top: 1rem; text-align: center; font-size: 0.9rem;">
                            <button type="button" id="resendForgotBtn" onclick="resendForgotCode()"
                                style="background: none; border: none; color: #1b4d3e; text-decoration: underline; cursor: pointer;">
                                Resend Code
                            </button>
                            <span id="forgotTimerSpan" style="display: none; color: #666;">
                                Resend in <span id="forgotTimer">60</span>s
                            </span>
                            <div id="resendForgotMsg" style="margin-top: 0.5rem; font-size: 0.85rem;"></div>
                        </div>
                    </form>
                    <div class="text-center mt-4 text-sm">
                        <a onclick="showLogin()" style="cursor: pointer;">Back to Login</a>
                    </div>
                </div>

                <!-- Confirm Account -->
                <div id="forgotStepConfirm" style="display:none; text-align: center;">
                    <p style="margin-bottom: 1.5rem; color: #666;">
                        This phone number is associated with the account:
                        <br><strong id="forgotConfirmUsername"
                            style="font-size: 1.2rem; color: var(--primary-color);"></strong>
                    </p>
                    <p style="margin-bottom: 1.5rem; color: #666;">Is this your account?</p>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <button type="button" onclick="confirmForgotPasswordAccount()" style="flex: 1;">Yes,
                            Continue</button>
                        <button type="button" onclick="showLogin()" style="flex: 1; background: #64748b;">No,
                            Cancel</button>
                    </div>
                    <div class="text-center mt-4 text-sm">
                        <a onclick="showLogin()" style="cursor: pointer;">Back to Login</a>
                    </div>
                </div>
                <div id="forgotStep3" style="display:none;">
                    <p style="text-align: center; margin-bottom: 1.5rem; color: #666;">
                        Enter your new password
                    </p>
                    <form id="forgotResetForm">
                        <div class="mb-2">
                            <label for="forgotNewPassword" class="visually-hidden">New Password</label>
                            <div class="password-input-wrapper">
                                <input type="password" id="forgotNewPassword" placeholder="New Password" required>
                                <button type="button" class="password-toggle"
                                    onclick="togglePassword('forgotNewPassword', this)"
                                    aria-label="Toggle password visibility">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="forgotConfirmPassword" class="visually-hidden">Confirm Password</label>
                            <div class="password-input-wrapper">
                                <input type="password" id="forgotConfirmPassword" placeholder="Confirm Password"
                                    required>
                                <button type="button" class="password-toggle"
                                    onclick="togglePassword('forgotConfirmPassword', this)"
                                    aria-label="Toggle password visibility">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit">Reset Password</button>
                    </form>
                    <div class="text-center mt-4 text-sm">
                        <a onclick="showLogin()" style="cursor: pointer;">Back to Login</a>
                    </div>
                </div>
            </div>

            <!-- Register Form -->
            <div id="registerSection" style="display:none;">
                <h2 class="text-center" style="margin-bottom: 1.5rem; color: var(--primary-color);">
                    <?php echo t('create_account'); ?>
                </h2>
                <form id="registerForm" enctype="multipart/form-data">
                    <label for="regUsername" class="visually-hidden">Username (Full Name)</label>
                    <input type="text" id="regUsername" placeholder="Username (Full Name e.g. Chisomo Banda)" required>

                    <label for="regPassword" class="visually-hidden"><?php echo t('password'); ?></label>
                    <div class="password-input-wrapper">
                        <input type="password" id="regPassword" placeholder="<?php echo t('password'); ?>" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('regPassword', this)"
                            aria-label="Toggle password visibility">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    
                    <label for="regConfirmPassword" class="visually-hidden">Confirm Password</label>
                    <div class="password-input-wrapper" style="margin-bottom: 1rem;">
                        <input type="password" id="regConfirmPassword" placeholder="Confirm Password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('regConfirmPassword', this)"
                            aria-label="Toggle password visibility">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <label for="regPhone" class="visually-hidden">Phone Number</label>
                    <input type="tel" id="regPhone" name="phone" placeholder="Phone Number" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 1rem;">

                    <label for="regRole" class="visually-hidden"><?php echo t('role'); ?></label>
                    <div
                        style="margin-bottom: 1rem; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; background: #f9f9f9;">
                        <label
                            style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--primary-color);">Select
                            Role(s):</label>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" name="roles" value="farmer" class="role-checkbox"
                                    style="margin-right: 0.5rem; width: 18px; height: 18px; cursor: pointer;">
                                <span><?php echo t('farmer'); ?></span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" name="roles" value="buyer" class="role-checkbox"
                                    style="margin-right: 0.5rem; width: 18px; height: 18px; cursor: pointer;">
                                <span><?php echo t('buyer'); ?></span>
                            </label>
                        </div>
                        <small style="display: block; margin-top: 0.5rem; color: #666;">You can select both if you want
                            to farm and buy produce</small>
                    </div>

                    <label for="regGender" class="visually-hidden">Gender</label>
                    <select id="regGender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>

                    <label for="regLocation" class="visually-hidden"><?php echo t('location'); ?></label>
                    <input type="text" id="regLocation" placeholder="e.g. Chikwawa" required>

                    <div id="nationalIdContainer" style="margin-bottom: 1rem; display: none;">
                        <label for="regNationalId" style="display: block; font-weight: 600; margin-bottom: 0.4rem; color: var(--primary-color); font-size: 0.95rem;">
                            <i class="fa-solid fa-id-card" style="margin-right: 6px;"></i>National ID Document
                        </label>
                        <input type="file" id="regNationalId" name="national_id"
                            accept="image/jpeg,image/png,image/gif,image/webp,application/pdf"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 6px; background: #f9f9f9; font-size: 0.9rem; cursor: pointer;">
                        <small style="display: block; margin-top: 0.4rem; color: #666;">
                            <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i>
                            Upload a clear photo or scan of your National ID (JPG, PNG, or PDF &mdash; max 5MB).
                        </small>
                    </div>

                    <div id="farmerIdContainer" style="margin-bottom: 1rem; display: none;">
                        <label for="regFarmerId" style="display: block; font-weight: 600; margin-bottom: 0.4rem; color: var(--primary-color); font-size: 0.95rem;">
                            <i class="fa-solid fa-hashtag" style="margin-right: 6px;"></i>Farmer ID
                        </label>
                        <input type="text" id="regFarmerId" name="farmer_id" placeholder="e.g. MW-SF-00001"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 6px; background: #f9f9f9; font-size: 0.9rem;">
                        <small style="display: block; margin-top: 0.4rem; color: #666;">
                            <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i>
                            Required to automatically verify your identity against Ministry of Agriculture records.
                        </small>
                    </div>

                    <button type="submit"><?php echo t('register'); ?></button>
                </form>
                <div class="text-center mt-4 text-sm">
                    Already have an account? <a onclick="showLogin()">Login here</a>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="assets/css/toast.css">
    <script src="assets/js/toast.js"></script>
    <script src="assets/js/script.js?v=<?php echo time(); ?>"></script>
</body>

</html>
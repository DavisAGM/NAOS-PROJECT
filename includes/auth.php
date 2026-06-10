<?php
/**
 * NAOS Session Bootstrap
 * Single authoritative place for session configuration.
 * Include this FIRST via auth.php — do not call session_start() anywhere else.
 */
if (session_status() === PHP_SESSION_NONE) {
    // Security headers for the session cookie
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', 1);   // Reject uninitialized session IDs
    ini_set('session.gc_maxlifetime', 7200); // 2-hour server-side lifetime

    session_start();
}

// ── Session idle timeout (30 minutes) ────────────────────────────────────────
define('SESSION_TIMEOUT', 1800); // 30 minutes

if (isset($_SESSION['user_id'])) {
    $now = time();

    if (isset($_SESSION['last_active']) && ($now - $_SESSION['last_active']) > SESSION_TIMEOUT) {
        // Session has expired — destroy cleanly
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();

        // For API callers return JSON; for page callers redirect
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'session_expired', 'message' => 'Your session has expired. Please log in again.']);
        } else {
            header('Location: /naos/auth.php?error=session_expired');
        }
        exit();
    }

    $_SESSION['last_active'] = $now;
}

// ── Auth helper functions ─────────────────────────────────────────────────────

if (!function_exists('isLoggedIn')) {
    function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('isUserActive')) {
    function isUserActive($conn, $userId)
    {
        if (!$userId) return false;
        $stmt = mysqli_prepare($conn, "SELECT is_active FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        return $user && (int) $user['is_active'] === 1;
    }
}

if (!function_exists('requireLogin')) {
    /**
     * Enforces login + optional role check.
     * Use this at the top of every protected page/API instead of duplicating the pattern.
     *
     * @param string|null $requiredRole  'farmer', 'buyer', 'admin', or null for any
     * @param bool        $isApi         true → return JSON 401; false → redirect
     */
    function requireLogin($requiredRole = null, $isApi = false)
    {
        if (!isLoggedIn()) {
            if ($isApi) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized', 'message' => 'Please log in.']);
                exit();
            }
            header('Location: /naos/auth.php');
            exit();
        }

        if ($requiredRole !== null) {
            $active = getActiveRole();
            if ($active !== $requiredRole) {
                if ($isApi) {
                    header('Content-Type: application/json');
                    http_response_code(403);
                    echo json_encode(['error' => 'Forbidden', 'message' => "This action requires the '{$requiredRole}' role."]);
                    exit();
                }
                // Redirect to the correct dashboard
                $map = ['farmer' => '/naos/farmer/dashboard.php', 'buyer' => '/naos/buyer/dashboard.php', 'admin' => '/naos/admin/dashboard.php'];
                header('Location: ' . ($map[$active] ?? '/naos/auth.php'));
                exit();
            }
        }
    }
}

if (!function_exists('checkSubscription')) {
    function checkSubscription($conn, $userId)
    {
        if (!$userId) return false;
        $stmt = mysqli_prepare($conn, "SELECT id FROM subscriptions WHERE user_id = ? AND status = 'active'");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }
}

if (!function_exists('getSubscriptionDetails')) {
    function getSubscriptionDetails($conn, $userId)
    {
        if (!$userId) return null;
        $stmt = mysqli_prepare($conn, "SELECT * FROM subscriptions WHERE user_id = ? AND status = 'active' ORDER BY id DESC LIMIT 1");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
}

if (!function_exists('getUserRole')) {
    function getUserRole()
    {
        return $_SESSION['role'] ?? null;
    }
}

if (!function_exists('getActiveRole')) {
    function getActiveRole()
    {
        return $_SESSION['active_role'] ?? $_SESSION['role'] ?? null;
    }
}

if (!function_exists('logActivity')) {
    function logActivity($conn, $message, $userId = null)
    {
        if (!$conn) return;
        if ($userId === null && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }
        $stmt = mysqli_prepare($conn, "INSERT INTO logs (message, user_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "si", $message, $userId);
        mysqli_stmt_execute($stmt);
    }
}

if (!function_exists('getUsername')) {
    function getUsername($conn, $userId)
    {
        if (!$userId) return null;
        $stmt = mysqli_prepare($conn, "SELECT username FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) return $row['username'];
        return null;
    }
}

if (!function_exists('getUserProfilePicture')) {
    function getUserProfilePicture($conn, $userId)
    {
        if (!$userId) return '';
        $stmt = mysqli_prepare($conn, "SELECT profile_picture FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) return $row['profile_picture'] ?: '';
        return '';
    }
}

if (!function_exists('getUserInitials')) {
    function getUserInitials($conn, $userId)
    {
        if (!$userId) return '?';
        $stmt = mysqli_prepare($conn, "SELECT username FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $name = trim($row['username']);
            $words = explode(' ', $name);
            $initials = '';
            foreach (array_slice($words, 0, 2) as $word) {
                if (!empty($word)) $initials .= strtoupper($word[0]);
            }
            return $initials ?: strtoupper(substr($name, 0, 1)) ?: '?';
        }
        return '?';
    }
}

if (!function_exists('renderAvatar')) {
    function renderAvatar($conn, $userId, $basePath = '../assets/images/profiles/', $id = 'headerProfilePic', $size = '40px')
    {
        $pic      = getUserProfilePicture($conn, $userId);
        $initials = getUserInitials($conn, $userId);
        $sizeNum  = (int) $size;
        $fontSize = round($sizeNum * 0.4) . 'px';
        $baseStyle = "width:$size; height:$size; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:2px solid #fff; box-shadow:0 2px 4px rgba(0,0,0,0.1); font-weight:700; font-family:sans-serif; text-align:center; overflow:hidden;";

        $usePicture = false;
        if ($pic) {
            $profilePath = realpath(__DIR__ . '/' . $basePath . $pic);
            if ($profilePath && is_file($profilePath)) $usePicture = true;
        }

        if ($usePicture) {
            $style = $baseStyle . "background: url('" . $basePath . htmlspecialchars($pic) . "') center/cover no-repeat; color:transparent;";
        } else {
            $style = $baseStyle . "background-color:#1b4d3e; background-image:linear-gradient(135deg,#1b4d3e,#2d7a5f); color:#ffffff; font-size:$fontSize; letter-spacing:0.5px;";
        }

        return '<div id="' . htmlspecialchars($id) . '" data-initials="' . htmlspecialchars($initials) . '" style="' . $style . '">'
             . htmlspecialchars($initials)
             . '</div>';
    }
}

if (!function_exists('getUserRoles')) {
    function getUserRoles($conn, $userId)
    {
        if (!$userId) return [];
        $stmt = mysqli_prepare($conn, "SELECT role_type FROM user_roles WHERE user_id = ? ORDER BY is_primary DESC");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $roles = [];
        while ($row = mysqli_fetch_assoc($result)) $roles[] = $row['role_type'];
        return $roles;
    }
}

if (!function_exists('hasRole')) {
    function hasRole($conn, $userId, $roleType)
    {
        if (!$userId || !$roleType) return false;
        $stmt = mysqli_prepare($conn, "SELECT id FROM user_roles WHERE user_id = ? AND role_type = ?");
        mysqli_stmt_bind_param($stmt, "is", $userId, $roleType);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }
}

if (!function_exists('switchActiveRole')) {
    function switchActiveRole($conn, $userId, $roleType)
    {
        if (!hasRole($conn, $userId, $roleType)) return false;
        $_SESSION['active_role'] = $roleType;
        $_SESSION['role']        = $roleType; // backward compat
        $stmt = mysqli_prepare($conn, "UPDATE users SET active_role = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $roleType, $userId);
        return mysqli_stmt_execute($stmt);
    }
}

if (!function_exists('assignRole')) {
    function assignRole($conn, $userId, $roleType, $isPrimary = false)
    {
        if (!$userId || !in_array($roleType, ['farmer', 'buyer'])) return false;
        if ($isPrimary) {
            $stmt = mysqli_prepare($conn, "UPDATE user_roles SET is_primary = 0 WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
        }
        $isPrimaryInt = $isPrimary ? 1 : 0;
        $stmt = mysqli_prepare($conn, "INSERT INTO user_roles (user_id, role_type, is_primary) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE is_primary = ?");
        mysqli_stmt_bind_param($stmt, "isii", $userId, $roleType, $isPrimaryInt, $isPrimaryInt);
        return mysqli_stmt_execute($stmt);
    }
}

if (!function_exists('canUserSell')) {
    function canUserSell($conn, $userId)
    {
        if (!hasRole($conn, $userId, 'farmer')) return false;
        $stmt = mysqli_prepare($conn, "SELECT status FROM subscriptions WHERE user_id = ? AND role_type = 'farmer' AND status = 'active' LIMIT 1");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }
}

if (!function_exists('canUserBuy')) {
    function canUserBuy($conn, $userId)
    {
        return hasRole($conn, $userId, 'buyer');
    }
}

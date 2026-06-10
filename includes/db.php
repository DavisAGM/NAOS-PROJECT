<?php
$host = 'localhost';
$db = 'naos';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, $charset);


if (!function_exists('getSetting')) {
    function getSetting($conn, $key, $defaultValue = '')
    {
        $key = mysqli_real_escape_string($conn, $key);
        $res = mysqli_query($conn, "SELECT setting_value FROM system_settings WHERE setting_key = '$key'");
        if ($res && $row = mysqli_fetch_assoc($res)) {
            return $row['setting_value'];
        }
        return $defaultValue;
    }
}

<?php
// This script handles the language switching
define('APP_LOADED', true);
require_once 'core/bootstrap.php';

$lang_code = $_GET['lang'] ?? '';

// Validate that the language exists and is active
$stmt = $db->prepare("SELECT id FROM languages WHERE code = ? AND is_active = 1");
$stmt->bind_param('s', $lang_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Language is valid, set session and cookie
    $_SESSION['lang'] = $lang_code;
    setcookie('lang', $lang_code, time() + (86400 * 30), "/"); // 30 days
}

// Redirect back to the previous page, or home if referer is not available
$redirect_url = $_SERVER['HTTP_REFERER'] ?? SITE_URL;
redirect($redirect_url);
?>

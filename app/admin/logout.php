<?php
define('ADMIN_AREA', true);
require_once __DIR__ . '/../core/bootstrap.php';

// Ensure this is a POST request to prevent CSRF logout
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    die('Invalid request method.');
}

// Validate the token
validate_csrf_token();

// Unset all of the session variables
$_SESSION = [];

// If it's desired to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Redirect to the login page
redirect(rtrim(SITE_URL, '/') . '/admin/login.php');
?>

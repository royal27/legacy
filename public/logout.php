<?php
// public/logout.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Unset all of the session variables.
$_SESSION = [];

// 2. If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Finally, destroy the session.
session_destroy();

// 4. Start a new session just to pass a message to the login page.
session_start();
$_SESSION['success_message'] = 'You have been logged out successfully.';

// 5. Redirect to the login page.
header('Location: /login.php');
exit();
?>

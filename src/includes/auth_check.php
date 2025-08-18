<?php
// This script checks if the user is authenticated.
// It should be included at the top of any restricted page.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // User is not logged in.
    // Store a message to be displayed on the login page.
    $_SESSION['errors'] = ['You must be logged in to view this page.'];
    // Redirect to the login page.
    header('Location: /login.php');
    exit();
}

// Optional: You could add logic here to check if the user's account is still active, etc.
?>

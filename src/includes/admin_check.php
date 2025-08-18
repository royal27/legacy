<?php
// This script ensures the user is a logged-in administrator.
// It should be included at the top of every page in the /admin/ directory.

// 1. First, ensure the user is logged in at all.
// auth_check.php also starts the session.
require_once __DIR__ . '/auth_check.php';

// 2. Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/user.php';

// 3. Establish a database connection
$conn = db_connect();
if (!$conn) {
    // This is a critical failure. Redirect with a generic error.
    $_SESSION['errors'] = ['A critical error occurred. Please try again later.'];
    // Log the detailed error for the admin.
    error_log("Admin check failed: Could not connect to the database.");
    header('Location: /dashboard.php');
    exit();
}

// 4. Check if the user has the 'Admin' role.
$is_admin = user_has_role($conn, $_SESSION['user_id'], 'Admin');

// The connection can be closed after the check.
$conn->close();

// 5. If the user is not an admin, redirect them.
if (!$is_admin) {
    $_SESSION['errors'] = ['You do not have permission to access this area.'];
    header('Location: /dashboard.php');
    exit();
}

// If the script reaches this point, the user is a verified administrator.
?>

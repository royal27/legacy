<?php
// Admin Login Processing Script
session_start();

// --- Pre-flight checks ---
// 1. Ensure this is a POST request.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 2. Ensure username and password are set.
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    $_SESSION['login_error'] = 'Please enter both username and password.';
    header('Location: index.php');
    exit;
}

// --- Core Includes ---
// We need the database connection.
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

// --- Process Login ---
$username = $_POST['username'];
$password = $_POST['password'];
$prefix = DB_PREFIX;

// Prepare a statement to prevent SQL injection.
$stmt = $mysqli->prepare("SELECT id, username, password, role, language FROM `{$prefix}users` WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify the password.
    if (password_verify($password, $user['password'])) {
        // Password is correct. Check if the user has an admin role.
        // For now, only 'founder' can log in.
        if ($user['role'] === 'founder') {
            // Login successful!
            // Regenerate session ID for security.
            session_regenerate_id(true);

            // Store user data in the session.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_language'] = $user['language']; // Store user's language preference

            // Redirect to the admin dashboard.
            header('Location: index.php');
            exit;
        } else {
            // User does not have admin privileges.
            $_SESSION['login_error'] = 'You do not have permission to access this area.';
            header('Location: index.php');
            exit;
        }
    }
}

// If we reach here, it means the username was not found or the password was incorrect.
$_SESSION['login_error'] = 'Invalid username or password.';
header('Location: index.php');
exit;

?>

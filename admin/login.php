<?php
// Admin Login Processing Script
session_start();

// --- Pre-flight checks ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    $_SESSION['login_error'] = 'Please enter both username and password.';
    header('Location: index.php');
    exit;
}

// --- Core Includes ---
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

// --- Process Login ---
$username = $_POST['username'];
$password = $_POST['password'];
$prefix = DB_PREFIX;

$stmt = $mysqli->prepare("SELECT id, username, password, role, language FROM `{$prefix}users` WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        if ($user['role'] === 'founder') {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_language'] = $user['language'];
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['login_error'] = 'You do not have permission to access this area.';
            header('Location: index.php');
            exit;
        }
    }
}

$_SESSION['login_error'] = 'Invalid username or password.';
header('Location: index.php');
exit;
?>

<?php
// src/controllers/login_controller.php

require_once __DIR__ . '/../../src/includes/csrf.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
csrf_validate_token();

// Redirect if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit();
}

$errors = [];
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Store input to repopulate form on error
$_SESSION['old_input'] = ['username' => $username];

// --- 1. Validation ---
if (empty($username) || empty($password)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Both username/email and password are required.']);
    exit();
}

// --- 2. Database Operations ---
$conn = db_connect();
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection error.']);
    exit();
}

// Fetch user by username or email
$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // --- 3. Verify Password ---
    if (password_verify($password, $user['password'])) {
        // Password is correct, start the session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        unset($_SESSION['old_input']);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful! Redirecting...',
            'redirect_url' => '/dashboard.php'
        ]);
        $stmt->close();
        $conn->close();
        exit();
    }
}

// If we reach here, login failed
header('Content-Type: application/json');
echo json_encode(['status' => 'error', 'message' => 'Invalid username/email or password.']);
$stmt->close();
$conn->close();
exit();
?>

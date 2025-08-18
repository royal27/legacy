<?php
// src/controllers/register_controller.php

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
    header('Location: /register.php');
    exit();
}

$errors = [];
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Store input to repopulate form on error
$_SESSION['old_input'] = [
    'username' => $username,
    'email' => $email,
];

// --- 1. Validation ---
if (empty($username)) {
    $errors[] = 'Username is required.';
}
// Basic username validation
elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    $errors[] = 'Username must be 3-20 characters long and contain only letters, numbers, and underscores.';
}

if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email is not valid.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
} elseif (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}

if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match.';
}

// If there are validation errors, redirect back
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: /register.php');
    exit();
}

// --- 2. Database Operations ---
$conn = db_connect();
if (!$conn) {
    $_SESSION['errors'] = ['Database connection error.'];
    header('Location: /register.php');
    exit();
}

// Check for existing user (prepared statement to prevent SQL injection)
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $errors[] = 'A user with that username or email already exists.';
    $_SESSION['errors'] = $errors;
    $stmt->close();
    $conn->close();
    header('Location: /register.php');
    exit();
}
$stmt->close();

// --- 3. Create User and Assign Role ---
// Hash password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the new user
$insert_stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$insert_stmt->bind_param("sss", $username, $email, $hashed_password);

if ($insert_stmt->execute()) {
    $new_user_id = $insert_stmt->insert_id;
    $insert_stmt->close();

    // Assign the default 'User' role
    $role_stmt = $conn->prepare("SELECT id FROM roles WHERE role_name = 'User'");
    $role_stmt->execute();
    $result = $role_stmt->get_result();
    if ($result->num_rows > 0) {
        $role = $result->fetch_assoc();
        $user_role_id = $role['id'];

        $assign_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
        $assign_stmt->bind_param("ii", $new_user_id, $user_role_id);
        $assign_stmt->execute();
        $assign_stmt->close();
    }
    $role_stmt->close();

    // Success: redirect to login page with a success message
    unset($_SESSION['old_input']);
    $_SESSION['success_message'] = 'Registration successful! You can now log in.';
    header('Location: /login.php');
} else {
    // Handle potential database error during insertion
    $_SESSION['errors'] = ['An unexpected error occurred during registration. Please try again.'];
    header('Location: /register.php');
}

$conn->close();
exit();
?>

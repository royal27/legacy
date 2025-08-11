<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once '../includes/connect.php';
require_once '../includes/functions.php';

// Fetch theme settings
$settings_result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'admin_theme'");
$admin_theme = $settings_result->fetch_assoc()['setting_value'] ?? 'default.css';

// Fetch available languages for switcher
$available_languages = $conn->query("SELECT * FROM languages ORDER BY name");

// Handle admin language switching
if (isset($_GET['set_admin_lang'])) {
    $_SESSION['admin_lang'] = $_GET['set_admin_lang'];
    // Redirect to remove the GET param from URL
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit();
}
$admin_lang = $_SESSION['admin_lang'] ?? 'en'; // Default to English
?>

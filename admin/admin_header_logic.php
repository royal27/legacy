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

// Fetch offers for admin ticker
$offers = $conn->query("SELECT * FROM offers ORDER BY id DESC");
?>

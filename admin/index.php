<?php
// Admin panel principal
// Gestionare roluri, permisiuni, pluginuri, teme, limbi etc

session_start();
require_once __DIR__ . '/../includes/config.php';

// Verifică rol (fondator/admin)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['fondator', 'admin'])) {
    header('Location: /dashboard');
    exit;
}

// Exemplu notificare toastr
echo '<script src="/assets/js/jquery.js"></script>';
echo '<script src="/assets/js/toastr.min.js"></script>';
echo '<link rel="stylesheet" href="/assets/css/dark.css">';
echo '<link rel="stylesheet" href="/assets/css/toastr.min.css">';
echo "<script>toastr.info('Admin Panel - Bine ai revenit!');</script>";

// Conținut admin panel (plugin manager, role manager, language manager etc)
?>
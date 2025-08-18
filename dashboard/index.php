<?php
// Dashboard utilizator
// Conținut dinamic, notificări toastr, acces pluginuri

session_start();
require_once __DIR__ . '/../includes/config.php';

// Exemplu notificare toastr
echo '<script src="/assets/js/jquery.js"></script>';
echo '<script src="/assets/js/toastr.min.js"></script>';
echo '<link rel="stylesheet" href="/assets/css/gradient.css">';
echo '<link rel="stylesheet" href="/assets/css/toastr.min.css">';
echo "<script>toastr.success('Bine ai venit, {$_SESSION['username']}!');</script>";

// Conținut dashboard (pluginuri, mesaje, profil, puncte, etc)
?>
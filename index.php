<?php
// Legacy CMS - Index
// Router pentru permalinks, verificare instalare, inițializare template, limbă etc

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/router.php';

// Verifică dacă site-ul e instalat
if (!file_exists(__DIR__ . '/install/installed.lock')) {
    // Redirect către instalare
    header('Location: /install/');
    exit;
}

// Inițializează sesiunea, limba, template, etc.
session_start();
$lang = $_SESSION['lang'] ?? getDefaultLanguage();
$template = $_SESSION['template'] ?? getDefaultTemplate();

// Router - Permalinks (ex: /dashboard, /admin, /chatroom)
handleRoute();

?>
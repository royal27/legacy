<?php
// Sistem instalare: alegere limbă, config DB, user fondator

session_start();

if (file_exists(__DIR__ . '/installed.lock')) {
    header('Location: /');
    exit;
}

$step = $_GET['step'] ?? 1;

if ($step == 1) {
    // Alegere limbă
    // ... formular HTML + procesare
} elseif ($step == 2) {
    // Configurare DB
    // ... formular HTML + procesare
} elseif ($step == 3) {
    // Creează user fondator
    // ... formular HTML + procesare
    // La final: file_put_contents('installed.lock', 'ok');
    // header('Location: /');
} else {
    // Finalizare instalare
    // ... mesaj succes
}

?>
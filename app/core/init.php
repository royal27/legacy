<?php

// Start the session on all pages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the configuration file
require_once __DIR__ . '/config.php';

// Autoloader for classes
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/'
    ];
    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// --- Core Application Objects ---

// Initialize Database connection
try {
    $db = new Database();
} catch (Exception $e) {
    die('Database connection could not be established: ' . $e->getMessage());
}

// Initialize Language system
Language::load();

// Initialize App Settings (like active template)
App::init();

// Initialize Authentication system
Auth::init();

// Load active plugins
PluginManager::load_active_plugins();

// The Router is now instantiated in the main index.php
// This makes the file cleaner and follows the single responsibility principle.
// The init.php is for loading resources, and index.php is for running the app.

?>

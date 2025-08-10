<?php
// Enable full error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Main entry point for the application.
 */

// Use the correct directory separator for all paths
define('DS', DIRECTORY_SEPARATOR);

// Start a session for user authentication and other features.
session_start();

// Define the path to the configuration file.
define('CONFIG_PATH', __DIR__ . DS . 'includes' . DS . 'config.php');

// Check if the site is installed. If not, redirect to the installer.
if (!file_exists(CONFIG_PATH)) {
    header('Location: install' . DS . 'index.php');
    exit;
}

// --- Core Includes ---
require_once CONFIG_PATH;
require_once __DIR__ . DS . 'includes' . DS . 'database.php';
require_once __DIR__ . DS . 'includes' . DS . 'functions.php';
require_once __DIR__ . DS . 'includes' . DS . 'language.php';
require_once __DIR__ . DS . 'includes' . DS . 'template.php';
require_once __DIR__ . DS . 'includes' . DS . 'hooks.php';
require_once __DIR__ . DS . 'includes' . DS . 'plugin_loader.php';
require_once __DIR__ . DS . 'includes' . DS . 'router.php';

?>

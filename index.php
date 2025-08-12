<?php
// We will define a constant for the config file path to check for it later.
// This file will be created by the installer.
define('CONFIG_FILE', __DIR__ . '/app/core/config.php');

// Check if the application is installed by looking for the config file.
if (!file_exists(CONFIG_FILE)) {
    // If the config file is not found, redirect to the installer.
    // The web server will handle redirecting from /install/ to /install/index.php
    header('Location: install/');
    exit; // Stop further execution
}

// If the application is installed, load the core application files.
require_once 'app/core/init.php';

// Instantiate the Router
$app = new Router();
?>

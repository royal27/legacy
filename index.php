<?php
/**
 * Main entry point for the application.
 */

// Start a session for user authentication and other features.
session_start();

// Define the path to the configuration file.
define('CONFIG_PATH', __DIR__ . '/includes/config.php');

// Check if the site is installed. If not, redirect to the installer.
if (!file_exists(CONFIG_PATH)) {
    header('Location: install/index.php');
    exit;
}

// --- Core Includes ---

// 1. Load the configuration file with database credentials.
require_once CONFIG_PATH;

// 2. Establish the database connection.
// This makes the $mysqli object available.
require_once __DIR__ . '/includes/database.php';

// 3. Load core functions.
require_once __DIR__ . '/includes/functions.php';

// 4. Load the language system.
require_once __DIR__ . '/includes/language.php';

// 5. Load the template system.
require_once __DIR__ . '/includes/template.php';

// 6. Load the hook system.
require_once __DIR__ . '/includes/hooks.php';

// 7. Load active plugins.
// Plugins will register their actions with the hook system.
require_once __DIR__ . '/includes/plugin_loader.php';

// 8. Handle the request using the router.
// The router will use the template system and hooks to display the page.
require_once __DIR__ . '/includes/router.php';

?>

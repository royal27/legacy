<?php
// This will be our main router/entry point.

// Turn on error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define a constant to prevent direct file access
define('APP_LOADED', true);

// Check if the website is installed
if (!file_exists('config.php')) {
    // If not installed, we'll set a flag and show a message.
    // The language system isn't loaded here, so we handle translation in the template.
    $page_title = "Site Not Installed";
    $template_to_load = 'templates/not_installed.php';
} else {
    // If installed, load the configuration and core files
    require_once 'config.php';
    require_once 'core/bootstrap.php'; // This loads the language system

    // Now we can use the trans() function
    $page_title = trans('welcome_message');
    $template_to_load = 'templates/home.php';
}

// Load the main template structure
include 'templates/header.php';
include $template_to_load;
include 'templates/footer.php';
?>

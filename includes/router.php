<?php
// This file handles routing for the application.

// Get the requested URI and parse it.
$request_uri = strtok($_SERVER['REQUEST_URI'], '?');

// Load the header for all pages.
load_template_part('header');

// Simple routing logic to load the correct page content.
switch ($request_uri) {
    case '/':
    case '/index.php':
        load_template_part('page-home');
        break;

    case '/dashboard':
        load_template_part('page-dashboard');
        break;

    // The /admin route is handled by the .htaccess redirecting to /admin/
    // So we don't need a case for it here.

    default:
        // Handle 404 Not Found
        http_response_code(404);
        load_template_part('page-404');
        break;
}

// Load the footer for all pages.
load_template_part('footer');

?>

<?php
// This file handles routing for the application.
$request_uri = strtok($_SERVER['REQUEST_URI'], '?');

load_template_part('header');

switch ($request_uri) {
    case '/':
    case '/index.php':
        load_template_part('page-home');
        break;
    case '/dashboard':
        load_template_part('page-dashboard');
        break;
    default:
        http_response_code(404);
        load_template_part('page-404');
        break;
}

load_template_part('footer');
?>

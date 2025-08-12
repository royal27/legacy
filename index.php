<?php
// Main router for the entire application
define('APP_LOADED', true);

// Turn on error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- Installation Check ---
if (!file_exists('config.php')) {
    $page_title = "Site Not Installed";
    include 'templates/header.php';
    include 'templates/not_installed.php';
    include 'templates/footer.php';
    exit();
}

// --- Bootstrap the application ---
require_once 'config.php';
require_once 'core/bootstrap.php';


// --- Routing Logic ---
$request_uri = $_GET['q'] ?? '';
$uri_parts = explode('/', $request_uri);

// Sanitize parts to prevent directory traversal
$uri_parts = array_map(function($part) {
    return basename($part);
}, $uri_parts);

$route = $uri_parts[0] ?: 'home';

switch ($route) {
    case 'home':
        $homepage_setting = $settings['homepage_display'] ?? 'default';
        if (strpos($homepage_setting, 'page-') === 0) {
            // A static page is set as the homepage
            $page_id = (int)str_replace('page-', '', $homepage_setting);
            $page_stmt = $db->prepare("SELECT slug FROM pages WHERE id = ?");
            $page_stmt->bind_param('i', $page_id);
            $page_stmt->execute();
            $page_res = $page_stmt->get_result();
            if ($page_res->num_rows > 0) {
                $_GET['slug'] = $page_res->fetch_assoc()['slug'];
                include 'page.php';
            } else {
                // Fallback to default if page not found
                include 'templates/header.php';
                include 'templates/home.php';
                include 'templates/footer.php';
            }
        } else {
            // Default homepage behavior
            $page_title = trans('welcome_message');
            include 'templates/header.php';
            include 'templates/home.php';
            include 'templates/footer.php';
        }
        break;

    case 'login':
        include 'login.php';
        break;

    case 'register':
        include 'register.php';
        break;

    case 'logout':
        include 'logout.php';
        break;

    case 'edit-profile':
        include 'edit-profile.php';
        break;

    case 'profile':
        // Expects /profile/123
        $_GET['id'] = $uri_parts[1] ?? 0;
        include 'profile.php';
        break;

    case 'page':
        // Expects /page/about-us
        $_GET['slug'] = $uri_parts[1] ?? '';
        include 'page.php';
        break;

    case 'language':
        // Expects /language/en
        $_GET['lang'] = $uri_parts[1] ?? '';
        include 'language.php';
        break;

    case 'profile-ajax':
        // This acts as the endpoint for profile AJAX calls, e.g., avatar upload
        include 'profile_ajax_handler.php';
        break;

    default:
        http_response_code(404);
        $page_title = "404 Not Found";
        // You can create a dedicated 404 template
        // For now, we'll just show a simple message
        include 'templates/header.php';
        echo '<div class="container message-box error"><h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p></div>';
        include 'templates/footer.php';
        break;
}
?>

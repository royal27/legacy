<?php
define('ADMIN_AREA', true);
require_once __DIR__ . '/../core/bootstrap.php';

// Must be logged in to access admin
if (!is_admin()) {
    redirect(SITE_URL . '/admin/login.php');
}

// Simple router for admin pages
$allowed_pages = ['dashboard', 'settings', 'users', 'edit_user', 'roles', 'permissions', 'languages', 'translations', 'plugins', 'points', 'security', 'menus', 'pages', 'edit_page', 'themes'];
$page = 'dashboard';
if (isset($_GET['page']) && in_array($_GET['page'], $allowed_pages)) {
    $page = $_GET['page'];
}

// Set page title based on the page slug
$page_title = ucwords(str_replace('_', ' ', $page));


// --- Output Buffering ---
// Start buffering. Any logic in the included page file (including redirects) will run
// before any output is sent to the browser.
ob_start();

$page_path = __DIR__ . '/pages/' . $page . '.php';
if (file_exists($page_path)) {
    include $page_path;
} else {
    $page_title = "404 Not Found";
    include __DIR__ . '/pages/404.php';
}

// Get the content from the buffer
$page_content = ob_get_clean();


// --- Page Rendering ---
// Now that all logic has run, we can safely include the layout
include __DIR__ . '/templates/header.php';

echo $page_content; // The content from the included page file

include __DIR__ . '/templates/footer.php';
?>

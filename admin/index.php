<?php
define('ADMIN_AREA', true);

// Bootstrap the application
require_once __DIR__ . '/../core/bootstrap.php';

// Security check: ensure user is logged in and is an admin
if (!is_admin()) {
    redirect('login.php');
}

// The page we want to load inside the admin layout.
// This can be expanded later into a router.
$page = $_GET['page'] ?? 'dashboard';

// --- Page Loading Logic ---
// Define the path to the admin pages/templates
$admin_pages_path = __DIR__ . '/pages/';

// Whitelist of allowed pages to prevent LFI attacks
$allowed_pages = [
    'dashboard'    => $admin_pages_path . 'dashboard.php',
    'languages'    => $admin_pages_path . 'languages.php',
    'translations' => $admin_pages_path . 'translations.php',
    'settings'     => $admin_pages_path . 'settings.php',
    'users'        => $admin_pages_path . 'users.php',
    'edit_user'    => $admin_pages_path . 'edit_user.php',
    'roles'        => $admin_pages_path . 'roles.php',
    'permissions'  => $admin_pages_path . 'permissions.php',
    'plugins'      => $admin_pages_path . 'plugins.php',
    'menus'        => $admin_pages_path . 'menus.php',
    'points'       => $admin_pages_path . 'points.php',
    'pages'        => $admin_pages_path . 'pages.php',
    'edit_page'    => $admin_pages_path . 'edit_page.php',
    'themes'       => $admin_pages_path . 'themes.php',
    'security'     => $admin_pages_path . 'security.php',
];

// Set the page title and the file to include
if (isset($allowed_pages[$page])) {
    $page_title = ucfirst($page);
    $page_to_include = $allowed_pages[$page];
} else {
    // If page not found, show a 404 or the dashboard
    $page_title = 'Page Not Found';
    http_response_code(404);
    // For now, we can create a 404 page or just show a message.
    // Let's create a simple 404 page in the pages folder.
    $page_to_include = $admin_pages_path . '404.php';
}


// Load the admin template
include __DIR__ . '/templates/header.php';
include $page_to_include;
include __DIR__ . '/templates/footer.php';

// Close the database connection
$db->close();
?>

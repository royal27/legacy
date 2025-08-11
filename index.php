<?php
// Core includes
require_once 'includes/connect.php';
require_once 'includes/functions.php';

// --- 1. Fetch Settings (needed for all pages) ---
$settings_result = $conn->query("SELECT * FROM settings");
$settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// --- 2. Basic Router ---
$path = $_GET['path'] ?? '';
$path_parts = explode('/', rtrim($path, '/'));

// --- 3. Fetch data common to all pages (template, languages, footer) ---
$active_template = $settings['active_template'] ?? 'default';
$languages_result = $conn->query("SELECT * FROM languages ORDER BY name");
$available_languages = [];
while ($row = $languages_result->fetch_assoc()) {
    $available_languages[] = $row;
}
$footer_pages = $conn->query("SELECT title, slug FROM pages WHERE show_in_footer = 1 ORDER BY title");


// --- ROUTING LOGIC ---

// Route: /page/{slug}
if ($path_parts[0] === 'page' && isset($path_parts[1])) {
    $slug = $path_parts[1];
    $stmt = $conn->prepare("SELECT * FROM pages WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo "404 Page Not Found";
        exit();
    }
    $page = $result->fetch_assoc();

    // Load a generic page template view
    include "templates/{$active_template}/page_template.php";

// Route: / (Homepage)
} else if (empty($path_parts[0])) {
    // Determine Current Language
    $default_lang = !empty($available_languages) ? $available_languages[0]['code'] : 'en';
    $lang = $default_lang;
    if (isset($_GET['lang']) && in_array($_GET['lang'], array_column($available_languages, 'code'))) {
        $lang = $_GET['lang'];
        setcookie('language', $lang, time() + (86400 * 30), "/");
    } elseif (isset($_COOKIE['language']) && in_array($_COOKIE['language'], array_column($available_languages, 'code'))) {
        $lang = $_COOKIE['language'];
    }

    // Fetch Homepage-specific data
    $menu_items_sql = "SELECT m.id, m.price, m.image, mt.name, mt.description
                       FROM menus m
                       JOIN menu_translations mt ON m.id = mt.menu_id
                       WHERE mt.language_code = ?";
    $stmt = $conn->prepare($menu_items_sql);
    $stmt->bind_param("s", $lang);
    $stmt->execute();
    $menu_items = $stmt->get_result();

    $gallery_images = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
    $offers = $conn->query("SELECT * FROM offers ORDER BY id DESC");

    // Load the main homepage template view
    include "templates/{$active_template}/template.php";

} else {
    // 404 Not Found
    http_response_code(404);
    echo "404 Page Not Found";
    exit();
}

// Close the connection
$conn->close();
?>

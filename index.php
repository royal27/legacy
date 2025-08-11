<?php
// Core includes
require_once 'includes/connect.php';
require_once 'includes/functions.php';

// --- Visitor Logging ---
$ip_address = $_SERVER['REMOTE_ADDR'];
$visit_date = date('Y-m-d');
$stmt_visitor = $conn->prepare("INSERT IGNORE INTO visitors (ip_address, visit_date) VALUES (?, ?)");
$stmt_visitor->bind_param("ss", $ip_address, $visit_date);
$stmt_visitor->execute();


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

// --- 4. Determine default language for content ---
$default_lang = !empty($available_languages) ? $available_languages[0]['code'] : 'en';

// Determine current language for this request
$lang = $default_lang;
if (isset($_GET['lang']) && in_array($_GET['lang'], array_column($available_languages, 'code'))) {
    $lang = $_GET['lang'];
} elseif (isset($_COOKIE['language']) && in_array($_COOKIE['language'], array_column($available_languages, 'code'))) {
    $lang = $_COOKIE['language'];
}

$footer_pages_sql = "SELECT p.slug, pt.title FROM pages p JOIN page_translations pt ON p.id = pt.page_id WHERE p.show_in_footer = 1 AND pt.language_code = ? ORDER BY pt.title";
$stmt_footer = $conn->prepare($footer_pages_sql);
$stmt_footer->bind_param("s", $lang);
$stmt_footer->execute();
$footer_pages = $stmt_footer->get_result();


// --- ROUTING LOGIC ---

// Route: /page/{slug}
if ($path_parts[0] === 'page' && isset($path_parts[1])) {
    $slug = $path_parts[1];

    // Determine language for content
    $content_lang = $default_lang;
    if (isset($_GET['lang']) && in_array($_GET['lang'], array_column($available_languages, 'code'))) {
        $content_lang = $_GET['lang'];
    } elseif (isset($_COOKIE['language']) && in_array($_COOKIE['language'], array_column($available_languages, 'code'))) {
        $content_lang = $_COOKIE['language'];
    }

    $sql = "SELECT p.id, p.slug, pt.title, pt.content
            FROM pages p
            JOIN page_translations pt ON p.id = pt.page_id
            WHERE p.slug = ? AND pt.language_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $slug, $content_lang);
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
    // Set cookie if lang is passed in URL
    if (isset($_GET['lang'])) {
        setcookie('language', $lang, time() + (86400 * 30), "/");
    }

    // Fetch Homepage-specific data
    $categories_sql = "SELECT mc.id, ct.name as category_name FROM menu_categories mc JOIN category_translations ct ON mc.id = ct.category_id WHERE ct.language_code = ? ORDER BY ct.name";
    $stmt_categories = $conn->prepare($categories_sql);
    $stmt_categories->bind_param("s", $lang);
    $stmt_categories->execute();
    $categories_result = $stmt_categories->get_result();

    $menu_by_category = [];
    while ($cat = $categories_result->fetch_assoc()) {
        $menu_by_category[$cat['category_name']] = [];
    }

    $menu_items_sql = "SELECT m.id, m.price, m.image, mt.name, mt.description, ct.name as category_name
                       FROM menus m
                       JOIN menu_translations mt ON m.id = mt.menu_id
                       LEFT JOIN menu_categories mc ON m.category_id = mc.id
                       LEFT JOIN category_translations ct ON mc.id = ct.category_id AND ct.language_code = ?
                       WHERE mt.language_code = ?
                       ORDER BY ct.name, m.id";
    $stmt = $conn->prepare($menu_items_sql);
    $stmt->bind_param("ss", $lang, $lang);
    $stmt->execute();
    $menu_items = $stmt->get_result();

    while ($item = $menu_items->fetch_assoc()) {
        $category_name = $item['category_name'] ?? 'Uncategorized';
        if (!isset($menu_by_category[$category_name])) {
            $menu_by_category[$category_name] = [];
        }
        $menu_by_category[$category_name][] = $item;
    }

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

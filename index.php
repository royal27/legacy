<?php
// Core includes
require_once 'includes/connect.php';
require_once 'includes/functions.php';

// --- 1. Fetch Settings ---
$settings_result = $conn->query("SELECT * FROM settings");
$settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// --- 2. Determine Active Template ---
$active_template = $settings['active_template'] ?? 'default';
$template_path = "templates/{$active_template}/template.php";

if (!file_exists($template_path)) {
    die("Error: Active template '{$active_template}' not found.");
}

// --- 3. Fetch Languages ---
$languages_result = $conn->query("SELECT * FROM languages ORDER BY name");
$available_languages = [];
while ($row = $languages_result->fetch_assoc()) {
    $available_languages[] = $row;
}

// --- 4. Determine Current Language ---
$lang = 'en'; // Default language
if (!empty($available_languages)) {
    $lang = $available_languages[0]['code']; // Default to the first available language
}
if (isset($_GET['lang']) && in_array($_GET['lang'], array_column($available_languages, 'code'))) {
    $lang = $_GET['lang'];
}

// --- 5. Fetch Menu Items for the current language ---
$sql = "SELECT m.id, m.price, m.image, mt.name, mt.description
        FROM menus m
        JOIN menu_translations mt ON m.id = mt.menu_id
        WHERE mt.language_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $lang);
$stmt->execute();
$menu_items = $stmt->get_result();

// --- 6. Fetch Gallery Images ---
$gallery_images = $conn->query("SELECT * FROM gallery ORDER BY id DESC");


// --- 7. Load the Template ---
// The template file will have access to all variables defined above:
// $conn, $settings, $active_template, $available_languages, $lang, $menu_items
include $template_path;

// Close the connection
$conn->close();
?>

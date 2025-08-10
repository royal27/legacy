<?php
// This file handles loading the active template.
if (!isset($mysqli)) {
    die("Database connection is not available for template system.");
}
define('DS', DIRECTORY_SEPARATOR);

$active_template_path = 'templates' . DS . 'default'; // Fallback default

$prefix = DB_PREFIX;
$sql = "SELECT `directory` FROM `{$prefix}templates` WHERE `is_active` = 1 LIMIT 1";
$result = $mysqli->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $active_template_path = 'templates' . DS . ltrim($row['directory'], '/\\');
}

/**
 * Loads a specific part of the template (e.g., 'header', 'footer', 'page-home').
 */
function load_template_part($part_name) {
    global $active_template_path, $mysqli;
    $file_path = __DIR__ . DS . '..' . DS . $active_template_path . DS . $part_name . '.php';

    if (file_exists($file_path)) {
        include $file_path;
    } else {
        echo "<p>Error: Template part '{$part_name}' not found at '{$file_path}'.</p>";
    }
}
?>

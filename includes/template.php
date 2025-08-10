<?php
// This file handles loading the active template.

if (!isset($mysqli)) {
    die("Database connection is not available for template system.");
}

// Global variable to hold the path to the active template directory.
$active_template_path = 'templates/default'; // Fallback default

// --- Determine the active template ---
$prefix = DB_PREFIX;
$sql = "SELECT `directory` FROM `{$prefix}templates` WHERE `is_active` = 1 LIMIT 1";
$result = $mysqli->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Make sure the path does not have a leading slash from the database
    $active_template_path = 'templates/' . ltrim($row['directory'], '/');
}

/**
 * Loads a specific part of the template (e.g., 'header', 'footer', 'page-home').
 *
 * @param string $part_name The name of the template file to load (without .php extension).
 */
function load_template_part($part_name) {
    global $active_template_path, $mysqli;
    $file_path = __DIR__ . '/../' . $active_template_path . '/' . $part_name . '.php';

    if (file_exists($file_path)) {
        include $file_path;
    } else {
        echo "<p>Error: Template part '{$part_name}' not found at '{$file_path}'.</p>";
    }
}
?>

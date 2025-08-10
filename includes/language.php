<?php
// This file handles the multi-language system.

// The $mysqli object from database.php is required.
if (!isset($mysqli)) {
    die("Database connection is not available.");
}

// Global array to hold all translation strings for the selected language.
$translations = [];
$current_language_code = 'en'; // Default fallback

// 1. Determine the language to use.
// Priority: User Preference (Session) > Cookie > Database Default

// Check for logged-in user's preference (we'll assume it's stored in a session).
if (isset($_SESSION['user_language'])) {
    $current_language_code = $_SESSION['user_language'];
}
// Else, check for a language cookie.
elseif (isset($_COOKIE['language'])) {
    $current_language_code = $_COOKIE['language'];
}
// Else, get the default language from the database.
else {
    $prefix = DB_PREFIX; // Get table prefix from config
    $sql = "SELECT code FROM `{$prefix}languages` WHERE is_default = 1 LIMIT 1";
    $result = $mysqli->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_language_code = $row['code'];
    }
}

// Store the determined language in the session for consistency.
$_SESSION['current_language'] = $current_language_code;

// 2. Fetch all translations for the determined language from the database.
$prefix = DB_PREFIX;
$stmt = $mysqli->prepare("SELECT translation_key, translation_value FROM `{$prefix}translations` WHERE lang_code = ?");
$stmt->bind_param('s', $current_language_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $translations[$row['translation_key']] = $row['translation_value'];
    }
}
$stmt->close();

// The $translations array is now populated and can be used with the t() function.
?>

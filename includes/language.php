<?php
// This file handles the multi-language system.
if (!isset($mysqli)) {
    die("Database connection is not available for the language system.");
}

$translations = [];
$current_language_code = 'en'; // Default fallback

if (isset($_SESSION['user_language'])) {
    $current_language_code = $_SESSION['user_language'];
} elseif (isset($_COOKIE['language'])) {
    $current_language_code = $_COOKIE['language'];
} else {
    $prefix = DB_PREFIX;
    $sql = "SELECT code FROM `{$prefix}languages` WHERE is_default = 1 LIMIT 1";
    $result = $mysqli->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_language_code = $row['code'];
    }
}

$_SESSION['current_language'] = $current_language_code;

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
?>

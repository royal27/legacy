<?php
// This file handles the database connection.

// The configuration file is expected to be included before this file.
if (!defined('DB_HOST')) {
    die("Error: Database configuration is not loaded. Please ensure config.php is included and correct.");
}

// Create a new mysqli object to connect to the database.
// The '@' symbol suppresses the default PHP warning on connection failure,
// allowing for custom error handling.
$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for a connection error.
if ($mysqli->connect_error) {
    // If there is an error, terminate the script and display a friendly message.
    // In a live environment, you might want to log this error instead of displaying it.
    die("Database connection failed. Please check your configuration. Error: " . $mysqli->connect_error);
}

// Set the character set to utf8mb4 to support a wide range of characters.
if (!$mysqli->set_charset("utf8mb4")) {
    // If setting the charset fails, display an error.
    printf("Error loading character set utf8mb4: %s\n", $mysqli->error);
    $mysqli->close();
    exit();
}

// The $mysqli object is now available for use in any script that includes this file.
?>

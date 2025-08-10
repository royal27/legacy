<?php
// This file handles the database connection.
if (!defined('DB_HOST')) {
    die("Error: Database configuration is not loaded.");
}

$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

if (!$mysqli->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", $mysqli->error);
    $mysqli->close();
    exit();
}
?>

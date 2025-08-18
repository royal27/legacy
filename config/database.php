<?php
// Database configuration
define('DB_HOST', '127.0.0.1'); // Using 127.0.0.1 instead of localhost can be faster in some environments
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');

/**
 * Establishes a database connection.
 *
 * @return mysqli|false A mysqli object on success, or false on failure.
 */
function db_connect() {
    // Enable error reporting for mysqli
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (mysqli_sql_exception $e) {
        // In a real application, you should handle this error gracefully,
        // log the error, and show a generic error message to the user.
        // For development, we can show the error.
        error_log($e->getMessage());
        // Never die with sensitive information in a production environment
        die("Database connection failed. Please check your configuration.");
    }
}
?>

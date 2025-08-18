<?php
// src/models/setting.php

/**
 * Gets all settings from the database and returns them as an associative array.
 * Keys are the setting_key, values are the setting_value.
 *
 * @param mysqli $conn The database connection object.
 * @return array An associative array of settings.
 */
function get_all_settings(mysqli $conn): array {
    $settings = [];
    $result = $conn->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $result->fetch_assoc()) {
        // Attempt to decode JSON strings (like for footer_links)
        $decoded_value = json_decode($row['setting_value'], true);
        $settings[$row['setting_key']] = (json_last_error() === JSON_ERROR_NONE) ? $decoded_value : $row['setting_value'];
    }
    return $settings;
}

/**
 * Updates a single setting in the database. Creates it if it doesn't exist.
 *
 * @param mysqli $conn The database connection object.
 * @param string $key The setting key.
 * @param string $value The setting value.
 * @return bool True on success, false on failure.
 */
function update_setting(mysqli $conn, string $key, string $value): bool {
    // Use INSERT ... ON DUPLICATE KEY UPDATE to handle both cases
    $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed in update_setting: " . $conn->error);
        return false;
    }

    $stmt->bind_param("ss", $key, $value);
    $result = $stmt->execute();
    $stmt->close();

    return $result;
}
?>

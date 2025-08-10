<?php
// This file will contain globally available functions.

/**
 * A placeholder for the translation function.
 * The actual translation logic will be handled by the language system.
 * This function will be populated after the language system is loaded.
 */
function t($key, $default = '') {
    // Access the global translations array
    global $translations;

    // Check if the key exists in the loaded translations
    if (isset($translations[$key])) {
        return $translations[$key];
    }

    // If not found, return the default value or the key itself
    return $default ?: $key;
}

// You can add other global helper functions here in the future.
?>

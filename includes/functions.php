<?php
// This file will contain globally available functions.

/**
 * The main translation function.
 * It accesses the global translations array loaded by the language system.
 *
 * @param string $key The key of the translation string.
 * @param string $default A default value to return if the key is not found.
 * @return string The translated string or the default value.
 */
function t($key, $default = '') {
    global $translations;

    if (isset($translations[$key])) {
        return $translations[$key];
    }

    // If no specific default is provided, return the key itself.
    return $default ?: $key;
}
?>

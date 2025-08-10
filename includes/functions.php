<?php
// This file will contain globally available functions.

/**
 * The main translation function.
 *
 * @param string $key The key of the translation string.
 * @param string $default A default value to return if the key is not found.
 * @return string The translated string or the default value.
 */
function t($key, $default = '') {
    global $translations;
    if (isset($translations[$key])) {
        return htmlspecialchars($translations[$key], ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars($default ?: $key, ENT_QUOTES, 'UTF-8');
}
?>

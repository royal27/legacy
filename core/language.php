<?php
// Prevent direct file access
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    die('Forbidden');
}

// Global array to hold all translations for the current language
$translations = [];

/**
 * Loads the language strings for a given language code.
 * The function determines which language to load based on session, cookie, or site default.
 *
 * @global mysqli $db The database connection object.
 * @global array $translations The array to populate with strings.
 */
function load_language() {
    global $db, $translations;

    $lang_code_to_load = null;

    // 1. Check for language in session
    if (isset($_SESSION['lang'])) {
        $lang_code_to_load = $_SESSION['lang'];
    }
    // 2. Check for language in cookie (and set session)
    elseif (isset($_COOKIE['lang'])) {
        $lang_code_to_load = $_COOKIE['lang'];
        $_SESSION['lang'] = $lang_code_to_load;
    }

    // 3. If no language is set, get the site default
    if ($lang_code_to_load === null) {
        $result = $db->query("SELECT value FROM settings WHERE name = 'default_lang' LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $default_lang_id = $result->fetch_assoc()['value'];
            $lang_result = $db->query("SELECT code FROM languages WHERE id = '{$default_lang_id}' LIMIT 1");
            if ($lang_result && $lang_result->num_rows > 0) {
                $lang_code_to_load = $lang_result->fetch_assoc()['code'];
            }
        }
    }

    // Fallback to English if everything else fails
    if ($lang_code_to_load === null) {
        $lang_code_to_load = 'en';
    }

    // Store in session and set cookie
    $_SESSION['lang'] = $lang_code_to_load;
    setcookie('lang', $lang_code_to_load, time() + (86400 * 30), "/"); // 30 days

    // 4. Load the strings from the database
    $stmt = $db->prepare(
        "SELECT ls.lang_key, ls.lang_value
         FROM language_strings ls
         JOIN languages l ON ls.lang_id = l.id
         WHERE l.code = ?"
    );
    $stmt->bind_param('s', $lang_code_to_load);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $translations[$row['lang_key']] = $row['lang_value'];
        }
    }
    $stmt->close();
}

/**
 * Retrieves a translated string from the global translations array.
 *
 * @global array $translations
 * @param string $key The language key to translate.
 * @param string|null $default A default value to return if the key is not found.
 * @return string The translated string, or the key/default value if not found.
 */
function trans(string $key, string $default = null): string {
    global $translations;
    if (isset($translations[$key])) {
        return htmlspecialchars($translations[$key]);
    }
    return htmlspecialchars($default ?? $key);
}
?>

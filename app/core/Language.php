<?php

class Language {
    private static $default_lang = 'en';
    private static $current_lang;
    private static $phrases = [];

    public static function load($lang = null) {
        if (is_null($lang)) {
            // Auto-detect language (session, cookie, browser etc.) - for now, we'll use a session or default
            self::$current_lang = $_SESSION['language'] ?? self::$default_lang;
        } else {
            self::$current_lang = $lang;
        }

        // Set session language
        $_SESSION['language'] = self::$current_lang;

        $lang_file = __DIR__ . '/../languages/' . self::$current_lang . '/main.php';

        if (file_exists($lang_file)) {
            self::$phrases = require($lang_file);
        } else {
            // Fallback to default language if the selected one doesn't exist
            $lang_file = __DIR__ . '/../languages/' . self::$default_lang . '/main.php';
            if (file_exists($lang_file)) {
                self::$phrases = require($lang_file);
            }
        }
    }

    public static function get($key, $default = '') {
        return self::$phrases[$key] ?? $default;
    }

    public static function getCurrentLanguage() {
        return self::$current_lang;
    }
}

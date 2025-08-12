<?php

/**
 * Session Class
 * Handles session-based flash messages for notifications.
 */
class Session {
    const FLASH_KEY = 'flash_messages';

    /**
     * Set a flash message.
     * @param string $key e.g., 'success', 'error', 'info'
     * @param string $message The message to display.
     */
    public static function flash($key, $message) {
        if (!isset($_SESSION[self::FLASH_KEY])) {
            $_SESSION[self::FLASH_KEY] = [];
        }
        $_SESSION[self::FLASH_KEY][$key] = $message;
    }

    /**
     * Check for flash messages and generate Toastr script.
     * Unsets the messages after they are displayed.
     */
    public static function display_flash_messages() {
        if (isset($_SESSION[self::FLASH_KEY])) {
            $messages = $_SESSION[self::FLASH_KEY];
            unset($_SESSION[self::FLASH_KEY]);

            echo "<script>";
            echo "toastr.options = {
                \"closeButton\": true,
                \"progressBar\": true,
                \"positionClass\": \"toast-top-right\"
            };";
            foreach ($messages as $type => $message) {
                // Sanitize message for JavaScript
                $message = addslashes($message);
                // Check if the type is a valid toastr type
                if (in_array($type, ['success', 'error', 'info', 'warning'])) {
                    echo "toastr['$type']('$message');";
                }
            }
            echo "</script>";
        }
    }
}

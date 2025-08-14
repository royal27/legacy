<?php

namespace App\Core;

class Session
{
    protected const FLASH_KEY = 'flash_messages';

    /**
     * Initializes the session if it's not already started.
     */
    public static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a session variable.
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session variable.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session variable exists.
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session variable.
     * @param string $key
     */
    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the entire session.
     */
    public static function destroy()
    {
        // Unset all of the session variables.
        $_SESSION = [];

        // If it's desired to kill the session, also delete the session cookie.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
    }

    /**
     * Set a flash message.
     * @param string $key
     * @param string $message
     */
    public static function flash($key, $message)
    {
        self::init();
        $_SESSION[self::FLASH_KEY][$key] = $message;
    }

    /**
     * Get a flash message and remove it.
     * @param string $key
     * @return string|null
     */
    public static function getFlash($key)
    {
        self::init();
        $message = $_SESSION[self::FLASH_KEY][$key] ?? null;
        unset($_SESSION[self::FLASH_KEY][$key]);
        return $message;
    }
}

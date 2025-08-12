<?php

class Auth {
    private static $user = null;
    private static $permissions = [];
    private static $is_founder = false;

    /**
     * Initialize the Auth class, load user data if logged in.
     */
    public static function init() {
        if (isset($_SESSION['user_id']) && is_null(self::$user)) {
            $userModel = new User();
            $userData = $userModel->findUserById($_SESSION['user_id']);

            if ($userData) {
                self::$user = $userData;
                self::$permissions = json_decode($userData['permissions'], true) ?? [];
                if (self::$user['role_id'] == 1 || isset(self::$permissions['all'])) {
                    self::$is_founder = true;
                }
            } else {
                // User in session not found in DB, force logout
                self::logout();
            }
        }
    }

    /**
     * Check if a user is logged in.
     * @return bool
     */
    public static function isLoggedIn() {
        return !is_null(self::$user);
    }

    /**
     * Get the current logged-in user's data.
     * @return array|null
     */
    public static function user() {
        return self::$user;
    }

    /**
     * Get the current logged-in user's ID.
     * @return int|null
     */
    public static function id() {
        return self::$user['id'] ?? null;
    }

    /**
     * Check if the current user has a specific permission.
     * @param string $permission The permission key to check (e.g., 'posts.edit').
     * @return bool
     */
    public static function check($permission) {
        if (!self::isLoggedIn()) {
            return false;
        }
        // Founder has all permissions
        if (self::$is_founder) {
            return true;
        }
        // Check for specific permission
        return isset(self::$permissions[$permission]) && self::$permissions[$permission] === true;
    }

    /**
     * Log the user out.
     */
    public static function logout() {
        self::$user = null;
        self::$permissions = [];
        self::$is_founder = false;
        unset($_SESSION['user_id']);
        // Consider session_destroy() for a full logout, but this is safer
        // if other session data needs to be preserved.
    }
}

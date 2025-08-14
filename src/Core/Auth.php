<?php

namespace App\Core;

/**
 * Authentication and Authorization Helper Class
 */
class Auth
{
    /**
     * Check if a user is logged in.
     *
     * @return bool
     */
    public static function check()
    {
        return Session::has('user_id');
    }

    /**
     * Get the currently logged-in user's ID.
     *
     * @return int|null
     */
    public static function id()
    {
        return Session::get('user_id');
    }

    /**
     * Check if the logged-in user has a specific permission.
     * The Founder (role_id=1) always has all permissions.
     *
     * @param string $permission_key The permission key to check for.
     * @return bool
     */
    public static function hasPermission($permission_key)
    {
        if (!self::check()) {
            return false;
        }

        // Founder has all permissions
        if (Session::get('role_id') == 1) {
            return true;
        }

        $permissions = Session::get('permissions', []);

        // Also check for the meta-permission
        return in_array($permission_key, $permissions) || in_array('permissions.manage', $permissions);
    }

    /**
     * Load user permissions into the session.
     *
     * @param int $role_id
     * @return void
     */
    public static function loadPermissions($role_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $sql = "SELECT p.permission_key
                FROM {$prefix}role_permissions rp
                JOIN {$prefix}permissions p ON rp.permission_id = p.id
                WHERE rp.role_id = ?";

        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $role_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $permissions_data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $permissions = array_column($permissions_data, 'permission_key');
        Session::set('permissions', $permissions);
    }
}

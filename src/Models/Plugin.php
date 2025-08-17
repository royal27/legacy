<?php

namespace App\Models;

use App\Core\Database;

class Plugin
{
    /**
     * Get all plugins from the database.
     *
     * @return array
     */
    public static function getAll()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $result = $db->query("SELECT * FROM {$prefix}plugins");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get a list of all active plugin directory names.
     *
     * @return array
     */
    public static function getActivePlugins()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $result = $db->query("SELECT directory_name FROM {$prefix}plugins WHERE is_active = 1");
        $plugins = $result->fetch_all(MYSQLI_ASSOC);
        return array_column($plugins, 'directory_name');
    }

    /**
     * Activate a plugin.
     *
     * @param string $directory_name
     * @return bool
     */
    public static function activate($directory_name)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        // Use INSERT ... ON DUPLICATE KEY UPDATE to handle both cases
        $sql = "INSERT INTO {$prefix}plugins (directory_name, is_active) VALUES (?, 1)
                ON DUPLICATE KEY UPDATE is_active = 1";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('s', $directory_name);
        return $stmt->execute();
    }

    /**
     * Deactivate a plugin.
     *
     * @param string $directory_name
     * @return bool
     */
    public static function deactivate($directory_name)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "UPDATE {$prefix}plugins SET is_active = 0 WHERE directory_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('s', $directory_name);
        return $stmt->execute();
    }
}

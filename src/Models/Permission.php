<?php

namespace App\Models;

use App\Core\Database;

/**
 * Permission Model
 *
 * Handles data operations for permissions.
 */
class Permission
{
    /**
     * Get all permissions from the database.
     *
     * @return array
     */
    public static function getAll()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $result = $db->query("SELECT id, permission_key, description FROM {$prefix}permissions ORDER BY permission_key ASC");

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

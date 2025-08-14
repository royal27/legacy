<?php

namespace App\Models;

use App\Core\Database;

/**
 * Role Model
 *
 * Handles data operations for roles.
 */
class Role
{
    /**
     * Get all roles from the database.
     *
     * @return array
     */
    public static function getAll()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $result = $db->query("SELECT id, name FROM {$prefix}roles ORDER BY id ASC");

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find a role by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $stmt = $db->prepare("SELECT * FROM {$prefix}roles WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $role = $result->fetch_assoc();
        $stmt->close();

        return $role;
    }

    /**
     * Create a new role.
     *
     * @param string $name The name of the role
     * @return bool
     */
    public static function create($name)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $stmt = $db->prepare("INSERT INTO {$prefix}roles (name) VALUES (?)");
        $stmt->bind_param('s', $name);

        return $stmt->execute();
    }

    /**
     * Delete a role.
     *
     * @param int $id The role ID
     * @return bool
     */
    public static function delete($id)
    {
        // Prevent deleting the first 2 roles (Founder, User)
        if ($id <= 2) {
            return false;
        }

        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $stmt = $db->prepare("DELETE FROM {$prefix}roles WHERE id = ?");
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }

    /**
     * Get the IDs of all permissions associated with a role.
     *
     * @param int $role_id
     * @return array
     */
    public static function getPermissions($role_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $stmt = $db->prepare("SELECT permission_id FROM {$prefix}role_permissions WHERE role_id = ?");
        $stmt->bind_param('i', $role_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $permissions = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Return a simple array of IDs
        return array_column($permissions, 'permission_id');
    }

    /**
     * Update the permissions for a given role.
     *
     * @param int $role_id
     * @param array $permission_ids
     * @return bool
     */
    public static function updatePermissions($role_id, $permission_ids)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        // Start transaction
        $db->begin_transaction();

        try {
            // Delete old permissions
            $stmt_delete = $db->prepare("DELETE FROM {$prefix}role_permissions WHERE role_id = ?");
            $stmt_delete->bind_param('i', $role_id);
            $stmt_delete->execute();
            $stmt_delete->close();

            // Insert new permissions
            if (!empty($permission_ids)) {
                $sql_insert = "INSERT INTO {$prefix}role_permissions (role_id, permission_id) VALUES (?, ?)";
                $stmt_insert = $db->prepare($sql_insert);
                foreach ($permission_ids as $permission_id) {
                    $stmt_insert->bind_param('ii', $role_id, $permission_id);
                    $stmt_insert->execute();
                }
                $stmt_insert->close();
            }

            // Commit transaction
            $db->commit();
            return true;

        } catch (\Exception $e) {
            $db->rollback();
            // In a real app, log the error
            return false;
        }
    }
}

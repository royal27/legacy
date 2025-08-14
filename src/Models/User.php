<?php

namespace App\Models;

use App\Core\Database;

/**
 * User Model
 *
 * Handles data operations for users.
 */
class User
{
    /**
     * Get all users from the database.
     *
     * @return array
     */
    public static function getAll()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $result = $db->query("SELECT u.id, u.username, u.email, r.name as role_name, u.created_at
                              FROM {$prefix}users u
                              LEFT JOIN {$prefix}roles r ON u.role_id = r.id
                              ORDER BY u.id ASC");

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find a user by their ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $stmt = $db->prepare("SELECT * FROM {$prefix}users WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user;
    }

    /**
     * Update a user's record in the database.
     *
     * @param int $id The user ID
     * @param array $data Associative array of data to update
     * @return bool True on success, false on failure
     */
    public static function update($id, $data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        // Fields that are allowed to be updated
        $allowed_fields = ['username', 'email', 'role_id', 'password'];

        $fields = [];
        $params = [];
        $types = '';

        foreach ($allowed_fields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                if ($field === 'password') {
                    $fields[] = 'password = ?';
                    $params[] = password_hash($data[$field], PASSWORD_DEFAULT);
                    $types .= 's';
                } else {
                    $fields[] = "$field = ?";
                    $params[] = $data[$field];
                    $types .= ($field === 'role_id') ? 'i' : 's';
                }
            }
        }

        if (empty($fields)) {
            return false; // Nothing to update
        }

        $params[] = $id;
        $types .= 'i';

        $sql = "UPDATE {$prefix}users SET " . implode(', ', $fields) . " WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);

        return $stmt->execute();
    }

    /**
     * Delete a user from the database.
     *
     * @param int $id The user ID
     * @return bool True on success, false on failure
     */
    public static function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $stmt = $db->prepare("DELETE FROM {$prefix}users WHERE id = ?");
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }

    /**
     * Create a new user in the database.
     *
     * @param array $data Associative array of user data
     * @return bool True on success, false on failure
     */
    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO {$prefix}users (username, email, password, role_id) VALUES (?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->bind_param('sssi', $data['username'], $data['email'], $password_hash, $data['role_id']);

        return $stmt->execute();
    }

    /**
     * Add points to a user's account.
     */
    public static function addPoints($user_id, $points)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("UPDATE {$prefix}users SET points = points + ? WHERE id = ?");
        $stmt->bind_param('ii', $points, $user_id);
        return $stmt->execute();
    }
}

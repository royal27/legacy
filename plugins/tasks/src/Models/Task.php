<?php

namespace Plugins\Tasks\Models;

use App\Core\Database;

class Task
{
    /**
     * Get all tasks.
     */
    public static function findAll()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "SELECT t.*, assigned.username as assigned_to_name, creator.username as creator_name
                FROM {$prefix}tasks t
                JOIN {$prefix}users assigned ON t.assigned_to_user_id = assigned.id
                JOIN {$prefix}users creator ON t.created_by_user_id = creator.id
                ORDER BY t.due_date ASC, t.created_at DESC";
        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find a single task by its ID.
     */
    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("SELECT * FROM {$prefix}tasks WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create a new task.
     */
    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "INSERT INTO {$prefix}tasks (assigned_to_user_id, created_by_user_id, title, description, status, due_date, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('iissss',
            $data['assigned_to_user_id'],
            $data['created_by_user_id'],
            $data['title'],
            $data['description'],
            $data['status'],
            $data['due_date']
        );
        return $stmt->execute();
    }

    /**
     * Update a task.
     */
    public static function update($id, $data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "UPDATE {$prefix}tasks SET
                assigned_to_user_id = ?,
                title = ?,
                description = ?,
                status = ?,
                due_date = ?
                WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('issssi',
            $data['assigned_to_user_id'],
            $data['title'],
            $data['description'],
            $data['status'],
            $data['due_date'],
            $id
        );
        return $stmt->execute();
    }

    /**
     * Delete a task.
     */
    public static function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("DELETE FROM {$prefix}tasks WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}

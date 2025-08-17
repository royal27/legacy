<?php

namespace Plugins\Forum\Models;

use App\Core\Database;

class Forum
{
    /**
     * Get all forums and categories, structured as a tree.
     *
     * @return array
     */
    public static function getHierarchy()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $result = $db->query("SELECT * FROM {$prefix}forums ORDER BY sort_order ASC");
        $forums = $result->fetch_all(MYSQLI_ASSOC);

        // Create a structured array with parent -> children
        $structured = [];
        foreach ($forums as $forum) {
            if ($forum['parent_id'] === null) {
                // This is a main category
                $structured[$forum['id']] = $forum;
                $structured[$forum['id']]['subforums'] = [];
            } else {
                // This is a sub-forum
                if (isset($structured[$forum['parent_id']])) {
                    $structured[$forum['parent_id']]['subforums'][] = $forum;
                }
            }
        }
        return $structured;
    }

    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("SELECT * FROM {$prefix}forums WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("INSERT INTO {$prefix}forums (parent_id, name, description, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('issi', $data['parent_id'], $data['name'], $data['description'], $data['sort_order']);
        return $stmt->execute();
    }

    public static function update($id, $data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("UPDATE {$prefix}forums SET parent_id = ?, name = ?, description = ?, sort_order = ? WHERE id = ?");
        $stmt->bind_param('issii', $data['parent_id'], $data['name'], $data['description'], $data['sort_order'], $id);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        // This is a simple delete. A real implementation should handle re-assigning topics/subforums.
        $stmt = $db->prepare("DELETE FROM {$prefix}forums WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}

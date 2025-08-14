<?php

namespace Plugins\Downloads\Models;

use App\Core\Database;

class DownloadCategory
{
    /**
     * Get all download categories.
     */
    public static function findAll()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "SELECT * FROM {$prefix}download_categories ORDER BY name ASC";
        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("SELECT * FROM {$prefix}download_categories WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function create($name, $description)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("INSERT INTO {$prefix}download_categories (name, description) VALUES (?, ?)");
        $stmt->bind_param('ss', $name, $description);
        return $stmt->execute();
    }

    public static function update($id, $name, $description)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("UPDATE {$prefix}download_categories SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param('ssi', $name, $description, $id);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        // Note: In a real app, we should handle what happens to files in a deleted category.
        $stmt = $db->prepare("DELETE FROM {$prefix}download_categories WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}

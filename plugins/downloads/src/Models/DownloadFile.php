<?php

namespace Plugins\Downloads\Models;

use App\Core\Database;

class DownloadFile
{
    /**
     * Get all validated files in a specific category.
     */
    public static function findAllByCategory($category_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "SELECT f.*, u.username
                FROM {$prefix}download_files f
                JOIN {$prefix}users u ON f.user_id = u.id
                WHERE f.category_id = ? AND f.is_validated = 1
                ORDER BY f.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find a single file by its ID.
     * @param bool $ignore_validation If true, finds the file even if not validated.
     */
    public static function findById($id, $ignore_validation = false)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $sql = "SELECT * FROM {$prefix}download_files WHERE id = ?";
        if (!$ignore_validation) {
            $sql .= " AND is_validated = 1";
        }

        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Increment the download count for a file.
     */
    public static function incrementDownloadCount($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("UPDATE {$prefix}download_files SET download_count = download_count + 1 WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    /**
     * Create a new file record.
     */
    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "INSERT INTO {$prefix}download_files
                (category_id, user_id, title, description, filename, filepath, filesize, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('iissssi',
            $data['category_id'],
            $data['user_id'],
            $data['title'],
            $data['description'],
            $data['filename'],
            $data['filepath'],
            $data['filesize']
        );
        return $stmt->execute();
    }

    public static function findAllUnvalidated()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "SELECT f.*, u.username, c.name as category_name
                FROM {$prefix}download_files f
                JOIN {$prefix}users u ON f.user_id = u.id
                JOIN {$prefix}download_categories c ON f.category_id = c.id
                WHERE f.is_validated = 0
                ORDER BY f.created_at ASC";
        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function validate($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("UPDATE {$prefix}download_files SET is_validated = 1 WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        // Also delete the physical file
        $file = self::findById($id, true); // findById needs to be adjusted to fetch non-validated files for this
        if ($file) {
            $full_path = dirname(__DIR__, 4) . '/public' . $file['filepath'];
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }
        $stmt = $db->prepare("DELETE FROM {$prefix}download_files WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}

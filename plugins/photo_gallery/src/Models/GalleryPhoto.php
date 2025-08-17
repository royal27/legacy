<?php

namespace Plugins\PhotoGallery\Models;

use App\Core\Database;

class GalleryPhoto
{
    /**
     * Get all validated photos in a specific album.
     */
    public static function findAllByAlbum($album_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "SELECT p.*, u.username
                FROM {$prefix}gallery_photos p
                JOIN {$prefix}users u ON p.user_id = u.id
                WHERE p.album_id = ? AND p.is_validated = 1
                ORDER BY p.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $album_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Other methods will be added later.
}

<?php

namespace Plugins\PhotoGallery\Models;

use App\Core\Database;

class GalleryAlbum
{
    /**
     * Get all public albums.
     */
    public static function getPublicAlbums()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "SELECT a.*, u.username
                FROM {$prefix}gallery_albums a
                JOIN {$prefix}users u ON a.user_id = u.id
                WHERE a.privacy_level = 0
                ORDER BY a.created_at DESC";
        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Other methods for finding by user, creating, etc. will be added later.
}

<?php

namespace Plugins\LiveChat\Models;

use App\Core\Database;

class ChatRoom
{
    /**
     * Get all public chat rooms.
     */
    public static function getPublicRooms()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $sql = "SELECT * FROM {$prefix}chat_rooms WHERE is_private = 0 ORDER BY name ASC";
        $result = $db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Other CRUD methods will be added later for the admin panel.
}

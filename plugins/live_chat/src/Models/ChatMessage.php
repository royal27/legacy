<?php

namespace Plugins\LiveChat\Models;

use App\Core\Database;

class ChatMessage
{
    /**
     * Get recent messages for a room.
     *
     * @param int $room_id
     * @param int $limit
     * @return array
     */
    public static function getMessages($room_id, $limit = 50)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $sql = "SELECT m.*, u.username
                FROM {$prefix}chat_messages m
                JOIN {$prefix}users u ON m.user_id = u.id
                WHERE m.room_id = ?
                ORDER BY m.created_at DESC
                LIMIT ?";

        $stmt = $db->prepare($sql);
        $stmt->bind_param('ii', $room_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return array_reverse($result->fetch_all(MYSQLI_ASSOC)); // Reverse to show oldest first
    }

    /**
     * Create a new chat message.
     *
     * @param array $data
     * @return bool
     */
    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $sql = "INSERT INTO {$prefix}chat_messages (room_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())";

        $stmt = $db->prepare($sql);
        $stmt->bind_param('iis', $data['room_id'], $data['user_id'], $data['content']);

        return $stmt->execute();
    }
}

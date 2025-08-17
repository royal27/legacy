<?php

namespace Plugins\LiveAlerts\Models;

use App\Core\Database;

class Alert
{
    /**
     * Find the first unread alert for a user.
     */
    public static function findUnreadByUserId($user_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("SELECT * FROM {$prefix}alerts WHERE user_id = ? AND is_read = 0 ORDER BY created_at ASC LIMIT 1");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Mark an alert as read.
     */
    public static function markAsRead($alert_id, $user_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        // Ensure user can only mark their own alerts as read
        $stmt = $db->prepare("UPDATE {$prefix}alerts SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->bind_param('ii', $alert_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Create a new alert.
     */
    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "INSERT INTO {$prefix}alerts (user_id, sent_by_user_id, content, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('iis', $data['user_id'], $data['sent_by_user_id'], $data['content']);
        return $stmt->execute();
    }
}

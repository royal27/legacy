<?php

namespace Plugins\SupportTickets\Models;

use App\Core\Database;

class TicketReply
{
    /**
     * Find all replies for a given ticket.
     */
    public static function findAllByTicketId($ticket_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "SELECT tr.*, u.username
                FROM {$prefix}ticket_replies tr
                JOIN {$prefix}users u ON tr.user_id = u.id
                WHERE tr.ticket_id = ?
                ORDER BY tr.created_at ASC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $ticket_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create a new ticket reply.
     */
    public static function create($data, $update_ticket = true)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "INSERT INTO {$prefix}ticket_replies (ticket_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('iis', $data['ticket_id'], $data['user_id'], $data['content']);
        $success = $stmt->execute();

        if ($success && $update_ticket) {
            // Update the parent ticket's last_updated_at timestamp
            $stmt_update = $db->prepare("UPDATE {$prefix}tickets SET last_updated_at = NOW() WHERE id = ?");
            $stmt_update->bind_param('i', $data['ticket_id']);
            $stmt_update->execute();
        }

        return $success;
    }
}

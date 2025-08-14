<?php

namespace Plugins\SupportTickets\Models;

use App\Core\Database;
use App\Core\Auth;

class Ticket
{
    /**
     * Get all tickets for a specific user.
     */
    public static function findAllByUserId($user_id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("SELECT * FROM {$prefix}tickets WHERE user_id = ? ORDER BY last_updated_at DESC");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all tickets for the admin view.
     */
    public static function findAll()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "SELECT t.*, u.username FROM {$prefix}tickets t JOIN {$prefix}users u ON t.user_id = u.id ORDER BY t.last_updated_at DESC";
        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find a single ticket by its ID.
     */
    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("SELECT t.*, u.username FROM {$prefix}tickets t JOIN {$prefix}users u ON t.user_id = u.id WHERE t.id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create a new ticket and its first reply.
     */
    public static function create($ticket_data, $reply_data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $db->begin_transaction();
        try {
            $sql_ticket = "INSERT INTO {$prefix}tickets (user_id, title, status, priority, created_at, last_updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
            $stmt_ticket = $db->prepare($sql_ticket);
            $stmt_ticket->bind_param('isss', $ticket_data['user_id'], $ticket_data['title'], $ticket_data['status'], $ticket_data['priority']);
            $stmt_ticket->execute();
            $new_ticket_id = $db->insert_id;
            $stmt_ticket->close();

            $reply_data['ticket_id'] = $new_ticket_id;
            TicketReply::create($reply_data, false); // Don't start a new transaction

            $db->commit();
            return $new_ticket_id;
        } catch (\Exception $e) {
            $db->rollback();
            return false;
        }
    }

    /**
     * Update a ticket's status.
     */
    public static function updateStatus($ticket_id, $status)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $stmt = $db->prepare("UPDATE {$prefix}tickets SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $ticket_id);
        return $stmt->execute();
    }
}

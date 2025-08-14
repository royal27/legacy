<?php

namespace Plugins\SupportTickets\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Core\Auth;
use App\Core\Session;
use Plugins\SupportTickets\Models\Ticket;
use Plugins\SupportTickets\Models\TicketReply;

class TicketController extends Controller
{
    public function __construct($route_params)
    {
        parent::__construct($route_params);
        if (!Auth::check()) {
            Session::flash('error', 'You must be logged in to view this page.');
            header('Location: /login');
            exit;
        }
    }

    /**
     * Display a list of the user's tickets.
     */
    public function index()
    {
        $tickets = Ticket::findAllByUserId(Auth::id());
        View::render('@support_tickets_frontend/index.php', [
            'title' => 'My Support Tickets',
            'tickets' => $tickets
        ]);
    }

    /**
     * Display a single ticket thread.
     */
    public function show()
    {
        $ticket_id = $this->route_params['id'];
        $ticket = Ticket::findById($ticket_id);

        // Security check: ensure the user owns this ticket or is an admin
        if ($ticket['user_id'] != Auth::id() && !Auth::hasPermission('tickets.manage')) {
             Session::flash('error', 'You do not have permission to view this ticket.');
             header('Location: /tickets');
             exit;
        }

        $replies = TicketReply::findAllByTicketId($ticket_id);
        View::render('@support_tickets_frontend/show.php', [
            'title' => 'Ticket #' . $ticket['id'] . ': ' . $ticket['title'],
            'ticket' => $ticket,
            'replies' => $replies
        ]);
    }

    /**
     * Show the form to create a new ticket.
     */
    public function new()
    {
        View::render('@support_tickets_frontend/new.php', ['title' => 'New Support Ticket']);
    }

    /**
     * Handle creation of a new ticket.
     */
    public function create()
    {
        $ticket_data = [
            'user_id' => Auth::id(),
            'title' => $_POST['title'],
            'status' => 'Open',
            'priority' => $_POST['priority']
        ];
        $reply_data = [
            'user_id' => Auth::id(),
            'content' => $_POST['content']
        ];

        $new_ticket_id = Ticket::create($ticket_data, $reply_data);
        if ($new_ticket_id) {
            Session::flash('success', 'Ticket created successfully.');
            header('Location: /tickets/' . $new_ticket_id);
            exit;
        } else {
            Session::flash('error', 'Failed to create ticket.');
            header('Location: /tickets/new');
            exit;
        }
    }

    /**
     * Handle a new reply from a user.
     */
    public function reply()
    {
        $ticket_id = $this->route_params['id'];
        $ticket = Ticket::findById($ticket_id);

        if ($ticket['user_id'] != Auth::id() && !Auth::hasPermission('tickets.manage')) {
             Session::flash('error', 'You do not have permission to reply to this ticket.');
             header('Location: /tickets');
             exit;
        }

        $data = [
            'ticket_id' => $ticket_id,
            'user_id' => Auth::id(),
            'content' => $_POST['content']
        ];

        TicketReply::create($data);
        Session::flash('success', 'Reply sent.');
        header('Location: /tickets/' . $ticket_id);
        exit;
    }
}

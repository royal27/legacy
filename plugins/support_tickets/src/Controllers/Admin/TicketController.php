<?php

namespace Plugins\SupportTickets\Controllers\Admin;

use App\Controllers\Admin\Controller;
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
        // We need a 'tickets.manage' permission
        // if (!Auth::hasPermission('tickets.manage')) {
        //     Session::flash('error', 'You do not have permission to manage tickets.');
        //     header('Location: ' . url('admin'));
        //     exit;
        // }
    }

    /**
     * Display a list of all tickets.
     */
    public function index()
    {
        $tickets = Ticket::findAll();
        View::render('@support_tickets_admin/index.php', [
            'title' => 'All Support Tickets',
            'tickets' => $tickets
        ]);
    }

    /**
     * Display a single ticket thread for an admin.
     */
    public function show()
    {
        $ticket_id = $this->route_params['id'];
        $ticket = Ticket::findById($ticket_id);
        $replies = TicketReply::findAllByTicketId($ticket_id);

        View::render('@support_tickets_admin/show.php', [
            'title' => 'Ticket #' . $ticket['id'] . ': ' . $ticket['title'],
            'ticket' => $ticket,
            'replies' => $replies
        ]);
    }

    /**
     * Handle updating a ticket's status or replying as staff.
     */
    public function update()
    {
        $ticket_id = $this->route_params['id'];

        // Update status if provided
        if (!empty($_POST['status'])) {
            Ticket::updateStatus($ticket_id, $_POST['status']);
        }

        // Add a reply if content is provided
        if (!empty($_POST['content'])) {
            $data = [
                'ticket_id' => $ticket_id,
                'user_id' => Auth::id(),
                'content' => $_POST['content']
            ];
            TicketReply::create($data);
        }

        Session::flash('success', 'Ticket updated.');
        header('Location: ' . url('admin/tickets/' . $ticket_id));
        exit;
    }
}

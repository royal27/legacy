<?php

namespace Plugins\LiveAlerts\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use Plugins\LiveAlerts\Models\Alert;

class ApiController extends Controller
{
    /**
     * Check for unread alerts for the current user.
     */
    public function check()
    {
        header('Content-Type: application/json');
        if (!Auth::check()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
            exit;
        }

        $alert = Alert::findUnreadByUserId(Auth::id());

        if ($alert) {
            echo json_encode(['success' => true, 'alert' => $alert]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No new alerts.']);
        }
        exit;
    }

    /**
     * Mark an alert as read.
     */
    public function markAsRead()
    {
        header('Content-Type: application/json');
        if (!Auth::check()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
            exit;
        }

        $alert_id = $this->route_params['id'];
        if (Alert::markAsRead($alert_id, Auth::id())) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to mark as read.']);
        }
        exit;
    }
}

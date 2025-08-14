<?php

namespace Plugins\LiveAlerts\Controllers\Admin;

use App\Controllers\Admin\Controller;
use App\Core\Auth;
use Plugins\LiveAlerts\Models\Alert;

class AlertController extends Controller
{
    /**
     * Handle sending an alert from the admin panel.
     */
    public function send()
    {
        header('Content-Type: application/json');

        // Basic permission check
        if (!Auth::hasPermission('users.edit')) { // Piggyback on another permission for now
            echo json_encode(['success' => false, 'message' => 'Permission denied.']);
            exit;
        }

        $data = [
            'user_id' => $_POST['user_id'] ?? 0,
            'sent_by_user_id' => Auth::id(),
            'content' => $_POST['content'] ?? ''
        ];

        if (empty($data['user_id']) || empty($data['content'])) {
            echo json_encode(['success' => false, 'message' => 'User ID and content are required.']);
            exit;
        }

        if (Alert::create($data)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send alert.']);
        }
        exit;
    }
}

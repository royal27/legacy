<?php

namespace Plugins\LiveChat\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use Plugins\LiveChat\Models\ChatMessage;

class ApiController extends Controller
{
    /**
     * Get new messages for a room.
     */
    public function getMessages()
    {
        header('Content-Type: application/json');

        $room_id = $this->route_params['room_id'] ?? 0;
        // We'd also get a 'last_message_id' to only fetch new ones.

        $messages = ChatMessage::getMessages($room_id);

        echo json_encode(['success' => true, 'messages' => $messages]);
        exit;
    }

    /**
     * Send a new message.
     */
    public function sendMessage()
    {
        header('Content-Type: application/json');

        if (!Auth::check()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
            exit;
        }

        $data = [
            'room_id' => $_POST['room_id'] ?? null,
            'user_id' => Auth::id(),
            'content' => $_POST['content'] ?? ''
        ];

        if (empty($data['room_id']) || empty($data['content'])) {
            echo json_encode(['success' => false, 'message' => 'Room ID and content are required.']);
            exit;
        }

        if (ChatMessage::create($data)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
        }
        exit;
    }
}

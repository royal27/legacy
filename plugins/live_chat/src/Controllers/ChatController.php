<?php

namespace Plugins\LiveChat\Controllers;

use App\Core\Controller;
use App\Core\View;
use Plugins\LiveChat\Models\ChatRoom;

class ChatController extends Controller
{
    /**
     * Display the main chat interface.
     */
    public function index()
    {
        $public_rooms = ChatRoom::getPublicRooms();

        View::render('@live_chat/index.php', [
            'title' => 'Live Chat',
            'rooms' => $public_rooms
        ]);
    }
}

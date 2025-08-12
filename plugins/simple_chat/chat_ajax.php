<?php
// This is a dedicated AJAX handler for the chat plugin.

// We need to bootstrap the main application to get access to DB and session
// The path is relative to this file's location: plugins/simple_chat/
require_once __DIR__ . '/../../core/bootstrap.php';

header('Content-Type: application/json');

// Security check: User must be logged in to use the chat
if (!is_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication required.']);
    exit;
}

$action = $_POST['action'] ?? '';
$response = ['status' => 'error', 'message' => 'Invalid action.'];
$current_user_id = (int)$_SESSION['user_id'];

switch ($action) {
    case 'send_message':
        $room_id = (int)($_POST['room_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');

        if ($room_id <= 0 || empty($message)) {
            $response['message'] = 'Invalid room or empty message.';
            break;
        }

        // Basic flood control (can be enhanced later)
        $last_msg_stmt = $db->prepare("SELECT timestamp FROM chat_messages WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1");
        $last_msg_stmt->bind_param('i', $current_user_id);
        $last_msg_stmt->execute();
        $last_msg_res = $last_msg_stmt->get_result();
        if ($last_msg_res->num_rows > 0) {
            $last_msg_time = new DateTime($last_msg_res->fetch_assoc()['timestamp']);
            $now = new DateTime();
            if ($now->getTimestamp() - $last_msg_time->getTimestamp() < 2) { // 2 second flood control
                $response['message'] = 'You are sending messages too fast.';
                break;
            }
        }

        $stmt = $db->prepare("INSERT INTO chat_messages (room_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $room_id, $current_user_id, $message);

        if ($stmt->execute()) {
            $response = ['status' => 'success'];
        } else {
            $response['message'] = 'Failed to send message.';
        }
        $stmt->close();
        break;

    case 'get_updates':
        $room_id = (int)($_POST['room_id'] ?? 0);
        $last_message_id = (int)($_POST['last_message_id'] ?? 0);

        if ($room_id <= 0) {
            $response['message'] = 'Invalid room.';
            break;
        }

        // Update user's 'last_active' status for the room.
        // This also adds them to the room if they aren't already in it.
        $activity_stmt = $db->prepare(
            "INSERT INTO chat_room_members (room_id, user_id, last_active) VALUES (?, ?, NOW())
             ON DUPLICATE KEY UPDATE last_active = NOW()"
        );
        $activity_stmt->bind_param('ii', $room_id, $current_user_id);
        $activity_stmt->execute();
        $activity_stmt->close();

        // Fetch new messages
        $messages_stmt = $db->prepare(
            "SELECT m.id, m.message, m.timestamp, u.username
             FROM chat_messages m
             JOIN users u ON m.user_id = u.id
             WHERE m.room_id = ? AND m.id > ?
             ORDER BY m.id ASC"
        );
        $messages_stmt->bind_param('ii', $room_id, $last_message_id);
        $messages_stmt->execute();
        $messages = $messages_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $messages_stmt->close();

        // Fetch online users (active in the last 2 minutes)
        $online_users_stmt = $db->prepare(
            "SELECT u.username
             FROM chat_room_members m
             JOIN users u ON m.user_id = u.id
             WHERE m.room_id = ? AND m.last_active > NOW() - INTERVAL 2 MINUTE
             ORDER BY u.username ASC"
        );
        $online_users_stmt->bind_param('i', $room_id);
        $online_users_stmt->execute();
        $online_users_res = $online_users_stmt->get_result();
        $online_users = [];
        while($row = $online_users_res->fetch_assoc()) {
            $online_users[] = $row['username'];
        }
        $online_users_stmt->close();

        $response = [
            'status' => 'success',
            'messages' => $messages,
            'online_users' => $online_users
        ];
        break;
}

echo json_encode($response);
exit;
?>

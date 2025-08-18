<?php
// AJAX chat API (GET = list messages, POST = send message)
// Rulare: /plugins/chat/api/messages.php
// Folosește sesiuni PHP pentru autentificare ($_SESSION['user_id'], $_SESSION['username'])
// DB: folosește constantele din includes/config.php (DB_HOST, DB_USER, DB_PASS, DB_NAME)

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/config.php';

// Connect (mysqli)
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// Basic helper
function json_exit($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// Simple flood control: limit messages per session
if (!isset($_SESSION['chat_sent_times'])) {
    $_SESSION['chat_sent_times'] = [];
}
// keep only last 60s
$_SESSION['chat_sent_times'] = array_filter($_SESSION['chat_sent_times'], function($t){
    return ($t > time() - 60);
});

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Params:
    // - last_id (optional) -> get messages with id > last_id
    // - limit (optional) -> max number, default 50
    // - room_id (optional) -> for future rooms (ignored for now)
    $last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
    $limit = isset($_GET['limit']) ? min(200, (int)$_GET['limit']) : 50;

    if ($last_id > 0) {
        $stmt = $mysqli->prepare("SELECT m.id, m.user_id, COALESCE(u.username, CONCAT('User#', m.user_id)) AS username, m.message, m.created_at FROM chat_messages m LEFT JOIN chat_users u ON m.user_id = u.id WHERE m.id > ? ORDER BY m.id ASC LIMIT ?");
        $stmt->bind_param('ii', $last_id, $limit);
    } else {
        $stmt = $mysqli->prepare("SELECT m.id, m.user_id, COALESCE(u.username, CONCAT('User#', m.user_id)) AS username, m.message, m.created_at FROM chat_messages m LEFT JOIN chat_users u ON m.user_id = u.id ORDER BY m.id DESC LIMIT ?");
        $stmt->bind_param('i', $limit);
    }

    if (!$stmt->execute()) {
        json_exit(['error' => 'Query failed'], 500);
    }
    $res = $stmt->get_result();
    $messages = [];
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }
    if ($last_id == 0) {
        $messages = array_reverse($messages);
    }
    json_exit(['messages' => $messages]);
}

if ($method === 'POST') {
    // Must be authenticated
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        json_exit(['error' => 'Unauthorized'], 401);
    }
    $user_id = (int)$_SESSION['user_id'];
    $username = trim($_SESSION['username']);

    // Flood control: max 20 messages per 60s per session
    if (count($_SESSION['chat_sent_times']) >= 20) {
        json_exit(['error' => 'Too many messages. Încearcă din nou mai târziu.'], 429);
    }

    $raw = $_POST['message'] ?? '';
    $message = trim($raw);
    if ($message === '') {
        json_exit(['error' => 'Empty message'], 400);
    }
    if (mb_strlen($message) > 5000) {
        json_exit(['error' => 'Message too long'], 400);
    }

    // Basic sanitation: store raw and escape on output. Also strip control chars.
    $message = preg_replace('/[\x00-\x1F\x7F]/u', '', $message);

    $stmt = $mysqli->prepare("INSERT INTO chat_messages (user_id, message, created_at) VALUES (?, ?, NOW())");
    if ($stmt === false) {
        json_exit(['error' => 'Prepare failed'], 500);
    }
    $stmt->bind_param('is', $user_id, $message);
    if (!$stmt->execute()) {
        json_exit(['error' => 'Insert failed'], 500);
    }
    $insert_id = $stmt->insert_id;

    // Append timestamp of send to session flood array
    $_SESSION['chat_sent_times'][] = time();

    // Return the inserted message (with username)
    $stmt2 = $mysqli->prepare("SELECT m.id, m.user_id, COALESCE(u.username, ?) AS username, m.message, m.created_at FROM chat_messages m LEFT JOIN chat_users u ON m.user_id = u.id WHERE m.id = ?");
    $fallbackName = "User#{$user_id}";
    $stmt2->bind_param('si', $fallbackName, $insert_id);
    if (!$stmt2->execute()) {
        json_exit(['error' => 'Select failed'], 500);
    }
    $res = $stmt2->get_result();
    $msg = $res->fetch_assoc();

    json_exit(['message' => $msg], 201);
}

json_exit(['error' => 'Method not allowed'], 405);
?>
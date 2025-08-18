<?php
// Simple chatroom UI (AJAX polling)
// Acces: /plugins/chat/chatroom.php
session_start();
require_once __DIR__ . '/../../includes/config.php';

// Dacă vrei, redirecționează la /login când nu e logat:
// if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Chatroom - <?= htmlspecialchars(SITE_NAME) ?></title>
  <link rel="stylesheet" href="/assets/css/chat.css">
  <link rel="stylesheet" href="/assets/css/toastr.min.css">
  <style> body { background: linear-gradient(135deg,#121217,#1e1330); min-height:100vh; } </style>
</head>
<body>
  <div class="chat-wrapper">
    <h2>Chatroom</h2>
    <div class="chat-container">
      <div id="chat-messages" class="chat-messages" aria-live="polite"></div>
      <form id="chat-form" class="chat-form" action="#" method="post">
        <input type="text" id="chat-input" placeholder="Scrie un mesaj..." autocomplete="off" maxlength="5000" />
        <button type="submit">Trimite</button>
      </form>
    </div>
  </div>

  <script src="/assets/js/jquery.js"></script>
  <script src="/assets/js/toastr.min.js"></script>
  <script src="/assets/js/chat.js"></script>
</body>
</html>

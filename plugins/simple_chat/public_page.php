<?php
// This file is loaded by the main router when a plugin route is matched.

// Ensure the main app is loaded
if (!defined('APP_LOADED')) {
    die('Forbidden');
}

// You can include a header and footer from the main theme
$page_title = 'Chat';
include_once BASE_PATH . '/templates/header.php';
?>

<div class="container">
    <h1>Simple AJAX Chat</h1>
    <p>This is the public page for the chat plugin. The chat interface would be loaded here.</p>

    <!-- Chat box placeholder -->
    <div id="chat-container" style="border: 1px solid #ccc; height: 400px; overflow-y: scroll; padding: 10px; margin-bottom: 10px;">
        <!-- Messages would appear here -->
    </div>
    <div id="chat-input">
        <input type="text" id="chat-message" placeholder="Type your message..." style="width: 80%; padding: 10px;">
        <button id="send-chat-message" class="btn btn-primary">Send</button>
    </div>
</div>

<?php
include_once BASE_PATH . '/templates/footer.php';
?>

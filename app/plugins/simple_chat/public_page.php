<?php
// This file is loaded by the main router when a plugin route is matched.

if (!defined('APP_LOADED')) {
    die('Forbidden');
}

// User must be logged in to see the chat
if (!is_logged_in()) {
    redirect(SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$page_title = 'Chat - Public Lobby';
include_once APP_PATH . '/templates/header.php';
?>

<style>
    .chat-wrapper { display: flex; height: 70vh; }
    .chat-sidebar { width: 25%; border-right: 1px solid #ccc; padding: 10px; overflow-y: auto; }
    .chat-main { width: 75%; display: flex; flex-direction: column; }
    .chat-messages { flex-grow: 1; padding: 10px; overflow-y: scroll; border-bottom: 1px solid #ccc; }
    .chat-input-area { padding: 10px; display: flex; }
    #chat-message-input { flex-grow: 1; padding: 8px; }
    #chat-send-btn { margin-left: 10px; }
    .message { margin-bottom: 10px; }
    .message .author { font-weight: bold; }
    .message .timestamp { font-size: 0.8em; color: #888; margin-left: 10px; }
    .online-user { padding: 5px 0; }
</style>

<div class="container">
    <h1>Simple AJAX Chat</h1>
    <div class="chat-wrapper">
        <aside class="chat-sidebar">
            <h3>Online Users</h3>
            <div id="online-users-list"></div>
        </aside>
        <main class="chat-main">
            <div id="chat-messages-container" class="chat-messages">
                <!-- Messages will be loaded here -->
            </div>
            <div class="chat-input-area">
                <input type="text" id="chat-message-input" placeholder="Type your message and press Enter...">
                <button id="chat-send-btn" class="btn btn-primary">Send</button>
            </div>
        </main>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const roomId = 1; // Default Public Lobby
    let lastMessageId = 0;
    const chatContainer = $('#chat-messages-container');
    const onlineUsersList = $('#online-users-list');
    const messageInput = $('#chat-message-input');
    const sendButton = $('#chat-send-btn');

    // Function to fetch updates from the server
    function getUpdates() {
        $.ajax({
            url: '<?php echo SITE_URL; ?>/plugins/simple_chat/chat_ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_updates',
                room_id: roomId,
                last_message_id: lastMessageId
            },
            success: function(response) {
                if (response.status === 'success') {
                    renderMessages(response.messages);
                    renderOnlineUsers(response.online_users);
                }
            },
            error: function() {
                // Silently fail on poll error, or add a subtle indicator
            },
            complete: function() {
                // Schedule the next poll
                setTimeout(getUpdates, 5000); // Poll every 5 seconds
            }
        });
    }

    // Function to render new messages
    function renderMessages(messages) {
        if (messages.length > 0) {
            messages.forEach(function(msg) {
                const messageHtml = `
                    <div class="message" data-id="${msg.id}">
                        <span class="author">${escapeHtml(msg.username)}:</span>
                        <span>${escapeHtml(msg.message)}</span>
                        <span class="timestamp">${new Date(msg.timestamp).toLocaleTimeString()}</span>
                    </div>
                `;
                chatContainer.append(messageHtml);
                lastMessageId = Math.max(lastMessageId, msg.id);
            });
            // Auto-scroll to the bottom
            chatContainer.scrollTop(chatContainer[0].scrollHeight);
        }
    }

    // Function to render the online users list
    function renderOnlineUsers(users) {
        onlineUsersList.empty();
        if (users.length > 0) {
            users.forEach(function(user) {
                onlineUsersList.append(`<div class="online-user">${escapeHtml(user)}</div>`);
            });
        } else {
            onlineUsersList.append('<span>No users online.</span>');
        }
    }

    // Function to send a message
    function sendMessage() {
        const message = messageInput.val().trim();
        if (message === '') {
            return;
        }

        sendButton.prop('disabled', true);

        $.ajax({
            url: '<?php echo SITE_URL; ?>/plugins/simple_chat/chat_ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'send_message',
                room_id: roomId,
                message: message,
                _token: '<?php echo $_SESSION['csrf_token']; ?>' // Assuming CSRF is needed, though less critical for chat
            },
            success: function(response) {
                if (response.status === 'success') {
                    messageInput.val('');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An unexpected error occurred while sending your message.');
            },
            complete: function() {
                sendButton.prop('disabled', false);
                messageInput.focus();
            }
        });
    }

    // Utility to escape HTML
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Event handlers
    sendButton.on('click', sendMessage);
    messageInput.on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            sendMessage();
        }
    });

    // Initial fetch and start polling
    getUpdates();
});
</script>

<?php
include_once APP_PATH . '/templates/footer.php';
?>

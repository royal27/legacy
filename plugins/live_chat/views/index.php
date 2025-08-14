<div class="chat-container">
    <div class="room-list">
        <h3>Rooms</h3>
        <ul>
            <?php foreach ($rooms as $room): ?>
                <li class="room-item" data-room-id="<?= $room['id'] ?>">
                    <strong><?= htmlspecialchars($room['name']) ?></strong>
                    <p><?= htmlspecialchars($room['description']) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="chat-area">
        <div class="chat-header">
            <h3 id="current-room-name">Select a room</h3>
        </div>
        <div class="message-list" id="message-list">
            <!-- Messages will be loaded here -->
            <div class="message-placeholder">Select a room to start chatting.</div>
        </div>
        <div class="message-input">
            <form id="message-form" style="display: none;">
                <input type="hidden" id="current-room-id" name="room_id" value="">
                <input type="text" id="message-content" name="content" placeholder="Type your message..." autocomplete="off" required>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
</div>

<!-- Add jQuery from a CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let currentRoomId = null;
    let messageFetchInterval = null;

    // Switch rooms
    $('.room-item').on('click', function() {
        currentRoomId = $(this).data('room-id');
        $('#current-room-id').val(currentRoomId);
        $('#current-room-name').text($(this).find('strong').text());
        $('#message-form').show();
        $('.room-item').removeClass('active');
        $(this).addClass('active');

        loadMessages();

        // Start polling for new messages
        if (messageFetchInterval) {
            clearInterval(messageFetchInterval);
        }
        messageFetchInterval = setInterval(loadMessages, 3000); // Poll every 3 seconds
    });

    // Load messages for the current room
    function loadMessages() {
        if (!currentRoomId) return;

        $.ajax({
            url: `/api/chat/messages/${currentRoomId}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const messageList = $('#message-list');
                    messageList.empty();
                    response.messages.forEach(msg => {
                        const messageHtml = `
                            <div class="message">
                                <strong>${msg.username}:</strong>
                                <p>${msg.content}</p>
                                <span class="timestamp">${msg.created_at}</span>
                            </div>
                        `;
                        messageList.append(messageHtml);
                    });
                    // Scroll to bottom
                    messageList.scrollTop(messageList[0].scrollHeight);
                }
            }
        });
    }

    // Send a message
    $('#message-form').on('submit', function(e) {
        e.preventDefault();
        const contentInput = $('#message-content');
        const message = contentInput.val();
        if (!message.trim()) return;

        $.ajax({
            url: '/api/chat/send',
            method: 'POST',
            data: {
                room_id: currentRoomId,
                content: message
            },
            success: function(response) {
                if (response.success) {
                    contentInput.val('');
                    loadMessages(); // Reload messages immediately
                } else {
                    alert(response.message || 'Failed to send message.');
                }
            }
        });
    });
});
</script>
<style>
/* Basic Chat Layout */
.chat-container { display: flex; height: 70vh; border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; background: rgba(0,0,0,0.2); }
.room-list { width: 250px; border-right: 1px solid rgba(255,255,255,0.2); padding: 10px; overflow-y: auto; }
.room-list h3 { margin-top: 0; }
.room-list ul { list-style: none; padding: 0; margin: 0; }
.room-item { padding: 10px; border-radius: 5px; cursor: pointer; }
.room-item:hover { background: rgba(255,255,255,0.1); }
.room-item.active { background: var(--primary-color); }
.room-item p { font-size: 0.8em; margin: 5px 0 0 0; color: rgba(255,255,255,0.6); }
.chat-area { flex: 1; display: flex; flex-direction: column; }
.chat-header { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.2); }
.chat-header h3 { margin: 0; }
.message-list { flex: 1; padding: 15px; overflow-y: auto; }
.message { margin-bottom: 10px; }
.message .timestamp { font-size: 0.75em; color: rgba(255,255,255,0.5); }
.message p { margin: 5px 0; padding: 10px; background: rgba(255,255,255,0.05); border-radius: 5px; display: inline-block; }
.message-input { padding: 15px; border-top: 1px solid rgba(255,255,255,0.2); }
#message-form { display: flex; }
#message-content { flex: 1; padding: 10px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.3); background: rgba(0,0,0,0.3); color: #fff; }
#message-form button { margin-left: 10px; padding: 10px 15px; border: none; background: var(--primary-color); color: #fff; border-radius: 5px; cursor: pointer; }
</style>

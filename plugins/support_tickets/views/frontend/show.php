<a href="<?= url('tickets') ?>"> &laquo; Back to My Tickets</a>
<h1><?= htmlspecialchars($title) ?></h1>

<div class="ticket-meta">
    <strong>Status:</strong> <span class="status-badge status-<?= strtolower($ticket['status']) ?>"><?= htmlspecialchars($ticket['status']) ?></span> |
    <strong>Priority:</strong> <?= htmlspecialchars($ticket['priority']) ?> |
    <strong>Created:</strong> <?= date('Y-m-d H:i', strtotime($ticket['created_at'])) ?>
</div>

<div class="ticket-thread">
    <?php foreach ($replies as $reply): ?>
        <div class="reply-item <?= ($reply['user_id'] == $ticket['user_id']) ? 'user-reply' : 'staff-reply' ?>">
            <div class="reply-header">
                <strong><?= htmlspecialchars($reply['username']) ?></strong> replied on <?= date('Y-m-d H:i', strtotime($reply['created_at'])) ?>
            </div>
            <div class="reply-content">
                <?= \App\Core\Formatting::format_post($reply['content']) ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<hr>

<div class="reply-form">
    <h3>Post a Reply</h3>
    <form action="<?= url('tickets/' . $ticket['id'] . '/reply') ?>" method="POST">
        <textarea name="content" rows="8" required></textarea>
        <button type="submit" class="btn">Submit Reply</button>
    </form>
</div>

<style>
.ticket-meta { margin: 10px 0 20px 0; font-size: 0.9em; }
.status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; color: #fff; }
.status-open { background-color: #3498db; }
.status-pending { background-color: #f1c40f; }
.status-resolved, .status-closed { background-color: #2ecc71; }
.ticket-thread { border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; }
.reply-item { border-bottom: 1px solid rgba(255,255,255,0.2); }
.reply-item:last-child { border-bottom: none; }
.reply-header { padding: 10px 15px; background: rgba(0,0,0,0.2); font-weight: 500; }
.reply-content { padding: 15px; line-height: 1.6; }
.staff-reply .reply-header { background: rgba(138, 43, 226, 0.3); } /* Highlight staff replies */
hr { border: none; border-top: 1px solid rgba(255,255,255,0.2); margin: 30px 0; }
.reply-form textarea { width: 100%; padding: 12px; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; color: #fff; font-size: 16px; box-sizing: border-box; margin-bottom: 10px; }
.btn { display: inline-block; padding: 12px 20px; background: var(--primary-color); border: none; border-radius: 8px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
</style>

<a href="/forum/<?= $topic['forum_id'] ?>"> &laquo; Back to Topic List</a>
<h1><?= htmlspecialchars($topic['title']) ?></h1>

<div class="post-list">
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-item" id="post-<?= $post['id'] ?>">
                <div class="post-author">
                    <strong><?= htmlspecialchars($post['author_name']) ?></strong><br>
                    <!-- User avatar/details can go here -->
                </div>
                <div class="post-content">
                    <div class="post-meta">
                        Posted on: <?= date('F j, Y, g:i a', strtotime($post['created_at'])) ?>
                    </div>
                    <div class="post-body">
                        <?= \App\Core\Formatting::format_post($post['content']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>There are no posts in this topic yet.</p>
    <?php endif; ?>
</div>

<hr>

<div class="reply-form">
    <h3>Post a Reply</h3>
    <form action="/topic/<?= $topic['id'] ?>/reply" method="POST">
        <div class="bbcode-toolbar">
            <!-- This could be made dynamic later -->
            <button type="button" class="bbcode-btn" title="Bold">[b]</button>
            <button type="button" class="bbcode-btn" title="Italic">[i]</button>
            <button type="button" class="bbcode-btn" title="Underline">[u]</button>
            <button type="button" class="bbcode-btn" title="Quote">[quote]</button>
        </div>
        <textarea name="content" id="reply-content" rows="8" required></textarea>
        <button type="submit" class="btn">Submit Reply</button>
    </form>
</div>
<script>
// Simple BBCode toolbar helper
document.querySelectorAll('.bbcode-btn').forEach(button => {
    button.addEventListener('click', function() {
        const textarea = document.getElementById('reply-content');
        const tag = this.innerText;
        const startTag = tag;
        const endTag = tag.replace('[', '[/');
        const selectedText = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
        const newText = startTag + selectedText + endTag;
        textarea.setRangeText(newText, textarea.selectionStart, textarea.selectionEnd, 'end');
        textarea.focus();
    });
});
</script>

<style>
.bbcode-toolbar {
    margin-bottom: 5px;
    background: rgba(0,0,0,0.2);
    padding: 5px;
    border-radius: 4px;
}
.bbcode-btn {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 3px;
    font-family: monospace;
}
.post-list { margin-top: 20px; }
.post-item {
    display: flex;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    margin-bottom: 15px;
    background: rgba(0,0,0,0.1);
}
.post-author {
    width: 150px;
    padding: 15px;
    border-right: 1px solid rgba(255,255,255,0.2);
    text-align: center;
}
.post-content {
    flex: 1;
    padding: 15px;
}
.post-meta {
    font-size: 0.8em;
    color: rgba(255,255,255,0.6);
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding-bottom: 10px;
    margin-bottom: 10px;
}
.post-body {
    line-height: 1.6;
}
.reply-form textarea {
    width: 100%;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    box-sizing: border-box;
    margin-bottom: 10px;
}
.btn { display: inline-block; padding: 12px 20px; background: var(--primary-color); border: none; border-radius: 8px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
hr { border: none; border-top: 1px solid rgba(255,255,255,0.2); margin: 30px 0; }
</style>

<a href="/forum/<?= $forum['id'] ?>"> &laquo; Back to Topic List</a>
<h1>New Topic in <?= htmlspecialchars($forum['name']) ?></h1>

<div class="new-topic-form">
    <form action="/forum/topic/create" method="POST">
        <input type="hidden" name="forum_id" value="<?= $forum['id'] ?>">

        <div class="form-group">
            <label for="title">Topic Title</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div class="form-group">
            <label for="content">First Post Content</label>
            <div class="bbcode-toolbar">
                <button type="button" class="bbcode-btn" title="Bold">[b]</button>
                <button type="button" class="bbcode-btn" title="Italic">[i]</button>
                <button type="button" class="bbcode-btn" title="Underline">[u]</button>
                <button type="button" class="bbcode-btn" title="Quote">[quote]</button>
            </div>
            <textarea name="content" id="topic-content" rows="10" required></textarea>
        </div>

        <button type="submit" class="btn">Create Topic</button>
    </form>
</div>

<script>
// Simple BBCode toolbar helper
document.querySelectorAll('.bbcode-btn').forEach(button => {
    button.addEventListener('click', function() {
        const textarea = document.getElementById('topic-content');
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
/* Reusing styles for consistency */
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
input[type="text"], textarea {
    width: 100%;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    box-sizing: border-box;
}
.btn { display: inline-block; padding: 12px 20px; background: var(--primary-color); border: none; border-radius: 8px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
</style>

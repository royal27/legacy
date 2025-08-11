<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

if (!user_has_permission('manage_settings')) {
    echo '<div class="message-box error">You do not have permission to manage pages.</div>';
    return;
}

$page_id = (int)($_GET['id'] ?? 0);
$is_editing = $page_id > 0;
$page = null;

if ($is_editing) {
    $stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->bind_param('i', $page_id);
    $stmt->execute();
    $page = $stmt->get_result()->fetch_assoc();
    if (!$page) {
        redirect('index.php?page=pages');
    }
}

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_page') {
    validate_csrf_token();
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $slug = trim($_POST['slug']);

    if (empty($slug)) {
        // Basic slug generation if empty
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }

    if (!empty($title)) {
        if ($is_editing) {
            $stmt = $db->prepare("UPDATE pages SET title = ?, content = ?, slug = ? WHERE id = ?");
            $stmt->bind_param('sssi', $title, $content, $slug, $page_id);
        } else {
            $stmt = $db->prepare("INSERT INTO pages (title, content, slug) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $title, $content, $slug);
        }

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Page saved successfully.'];
            redirect('index.php?page=pages');
        } else {
            $message = 'Error saving page. The slug might already exist.';
            $message_type = 'error';
        }
    } else {
        $message = 'Title is required.';
        $message_type = 'error';
    }
}

?>

<?php if (isset($message)): ?>
<div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="content-block">
    <a href="index.php?page=pages">&larr; Back to Page List</a>
    <h2><?php echo $is_editing ? 'Edit Page' : 'Add New Page'; ?></h2>

    <form action="index.php?page=edit_page<?php echo $is_editing ? '&id='.$page_id : ''; ?>" method="post">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="save_page">

        <div class="form-group">
            <label for="title">Page Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($page['title'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="slug">URL Slug</label>
            <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($page['slug'] ?? ''); ?>">
            <small>A unique, URL-friendly identifier. E.g., "about-us". Leave blank to auto-generate.</small>
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" rows="15"><?php echo htmlspecialchars($page['content'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Page</button>
    </form>
</div>

<script>
$(document).ready(function() {
    function generateSlug(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')       // Replace spaces with -
            .replace(/[^\w\-]+/g, '')   // Remove all non-word chars
            .replace(/\-\-+/g, '-')     // Replace multiple - with single -
            .replace(/^-+/, '')         // Trim - from start of text
            .replace(/-+$/, '');        // Trim - from end of text
    }

    $('#title').on('keyup', function() {
        // Only auto-generate if we are NOT in edit mode, to avoid overwriting a custom slug
        <?php if (!$is_editing): ?>
        $('#slug').val(generateSlug($(this).val()));
        <?php endif; ?>
    });
});
</script>

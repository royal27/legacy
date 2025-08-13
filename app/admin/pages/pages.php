<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

// I'll add a 'manage_pages' permission later. For now, use manage_settings.
if (!user_has_permission('manage_settings')) {
    echo '<div class="message-box error">You do not have permission to manage pages.</div>';
    return;
}

$message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

// --- Handle page deletion ---
if (isset($_POST['action']) && $_POST['action'] === 'delete_page') {
    validate_csrf_token();
    $page_id_to_delete = (int)$_POST['page_id'];
    $stmt = $db->prepare("DELETE FROM pages WHERE id = ?");
    $stmt->bind_param('i', $page_id_to_delete);
    if ($stmt->execute()) {
        $message = ['type' => 'success', 'text' => 'Page deleted successfully.'];
    } else {
        $message = ['type' => 'error', 'text' => 'Error deleting page.'];
    }
    $stmt->close();
}


// Fetch all pages
$pages = $db->query("SELECT * FROM pages ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

?>

<?php if ($message): ?>
<div class="message-box <?php echo $message['type']; ?>"><?php echo $message['text']; ?></div>
<?php endif; ?>

<div class="content-block">
    <div style="display:flex; justify-content: space-between; align-items: center;">
        <h2>Manage Pages</h2>
        <a href="index.php?page=edit_page" class="btn btn-secondary">Add New Page</a>
    </div>
    <p>Here you can manage the static pages of your website.</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page): ?>
            <tr>
                <td><?php echo $page['id']; ?></td>
                <td><strong><a href="<?php echo SITE_URL; ?>/page/<?php echo htmlspecialchars($page['slug']); ?>" target="_blank"><?php echo htmlspecialchars($page['title']); ?></a></strong></td>
                <td><?php echo htmlspecialchars($page['slug']); ?></td>
                <td><?php echo date('F j, Y', strtotime($page['created_at'])); ?></td>
                <td>
                    <a href="index.php?page=edit_page&id=<?php echo $page['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                    <form action="index.php?page=pages" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this page?');">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="delete_page">
                        <input type="hidden" name="page_id" value="<?php echo $page['id']; ?>">
                        <button type="submit" class="btn btn-accent btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
             <?php if (empty($pages)): ?>
                <tr><td colspan="5">No pages created yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

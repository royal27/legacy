<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

$message = '';
$message_type = '';

// --- Handle POST Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token();
    // --- Add a new language ---
    if (isset($_POST['action']) && $_POST['action'] === 'add_language') {
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);

        if (!empty($name) && !empty($code)) {
            $stmt = $db->prepare("INSERT INTO languages (name, code, is_active) VALUES (?, ?, 1)");
            $stmt->bind_param('ss', $name, $code);
            if ($stmt->execute()) {
                $message = 'Language added successfully.';
                $message_type = 'success';
            } else {
                $message = 'Error adding language. The code might already exist.';
                $message_type = 'error';
            }
            $stmt->close();
        } else {
            $message = 'Name and code are required.';
            $message_type = 'error';
        }
    }

    // --- Set default language ---
    if (isset($_POST['action']) && $_POST['action'] === 'set_default') {
        $lang_id = (int)$_POST['lang_id'];
        $stmt = $db->prepare("UPDATE settings SET value = ? WHERE name = 'default_lang'");
        $stmt->bind_param('s', $lang_id);
        if ($stmt->execute()) {
            $message = 'Default language updated successfully.';
            $message_type = 'success';
        } else {
            $message = 'Error updating default language.';
            $message_type = 'error';
        }
        $stmt->close();
    }

    // --- Delete a language ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete_language') {
        $lang_id = (int)$_POST['lang_id'];
        // Prevent deleting the default language or language ID 1 (usually English)
        $res = $db->query("SELECT value FROM settings WHERE name = 'default_lang'")->fetch_assoc()['value'];
        if ($lang_id == $res) {
             $message = 'You cannot delete the default language.';
             $message_type = 'error';
        } elseif ($lang_id == 1) {
            $message = 'You cannot delete the primary language (ID 1).';
            $message_type = 'error';
        }
        else {
            // Delete language strings first
            $stmt_strings = $db->prepare("DELETE FROM language_strings WHERE lang_id = ?");
            $stmt_strings->bind_param('i', $lang_id);
            $stmt_strings->execute();
            $stmt_strings->close();

            // Delete the language
            $stmt_lang = $db->prepare("DELETE FROM languages WHERE id = ?");
            $stmt_lang->bind_param('i', $lang_id);
            if($stmt_lang->execute()){
                 $message = 'Language deleted successfully.';
                 $message_type = 'success';
            } else {
                 $message = 'Error deleting language.';
                 $message_type = 'error';
            }
            $stmt_lang->close();
        }
    }
}

// --- Fetch Data for Display ---
$languages = $db->query("SELECT * FROM languages ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$default_lang_id = $db->query("SELECT value FROM settings WHERE name = 'default_lang'")->fetch_assoc()['value'];

?>

<?php if ($message): ?>
<div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="content-block">
    <h2>Manage Languages</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Default</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($languages as $lang): ?>
            <tr>
                <td><?php echo htmlspecialchars($lang['name']); ?></td>
                <td><?php echo htmlspecialchars($lang['code']); ?></td>
                <td>
                    <?php if ($lang['id'] == $default_lang_id): ?>
                        <span class="status-badge success">Default</span>
                    <?php else: ?>
                        <form action="index.php?page=languages" method="post" style="display:inline;">
                            <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="action" value="set_default">
                            <input type="hidden" name="lang_id" value="<?php echo $lang['id']; ?>">
                            <button type="submit" class="btn-link">Set as Default</button>
                        </form>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?page=translations&lang_id=<?php echo $lang['id']; ?>" class="btn btn-primary btn-sm">Translate</a>
                    <!-- Edit functionality can be added here -->
                    <form action="index.php?page=languages" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this language and all its translations? This cannot be undone.');">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="delete_language">
                        <input type="hidden" name="lang_id" value="<?php echo $lang['id']; ?>">
                        <button type="submit" class="btn btn-accent btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="content-block">
    <h2>Add New Language</h2>
    <form action="index.php?page=languages" method="post">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="add_language">
        <div class="form-group">
            <label for="name">Language Name (e.g., "French")</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="code">Language Code (e.g., "fr")</label>
            <input type="text" id="code" name="code" required>
        </div>
        <button type="submit" class="btn btn-secondary">Add Language</button>
    </form>
</div>

<style>
.content-block { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6; }
.data-table th { background-color: #f8f9fa; }
.status-badge { padding: 3px 8px; border-radius: 10px; color: white; font-size: 0.8em; }
.status-badge.success { background-color: #28a745; }
.btn-sm { padding: 5px 10px; font-size: 0.9em; }
.btn-link { background: none; border: none; color: var(--color-primary); cursor: pointer; text-decoration: underline; padding: 0; }
</style>

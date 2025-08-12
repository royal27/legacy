<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

// Security Check
if (!user_has_permission('manage_roles')) {
    echo '<div class="message-box error">You do not have permission to manage permissions.</div>';
    return;
}

$message = '';
$message_type = '';

// --- Handle POST Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token();
    // --- Add a new permission ---
    if (isset($_POST['action']) && $_POST['action'] === 'add_permission') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);

        if (!empty($name)) {
            $stmt = $db->prepare("INSERT INTO permissions (name, description) VALUES (?, ?)");
            $stmt->bind_param('ss', $name, $description);
            if ($stmt->execute()) {
                $message = 'Permission added successfully.';
                $message_type = 'success';
            } else {
                $message = 'Error adding permission. The name might already exist.';
                $message_type = 'error';
            }
            $stmt->close();
        } else {
            $message = 'Permission name is required.';
            $message_type = 'error';
        }
    }

    // --- Delete a permission ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete_permission') {
        $perm_id = (int)$_POST['perm_id'];

        $db->begin_transaction();
        try {
            // Delete from role_permissions first
            $stmt1 = $db->prepare("DELETE FROM role_permissions WHERE permission_id = ?");
            $stmt1->bind_param('i', $perm_id);
            $stmt1->execute();
            $stmt1->close();

            // Delete the permission itself
            $stmt2 = $db->prepare("DELETE FROM permissions WHERE id = ?");
            $stmt2->bind_param('i', $perm_id);
            $stmt2->execute();
            $stmt2->close();

            $db->commit();
            $message = 'Permission deleted successfully.';
            $message_type = 'success';
        } catch (mysqli_sql_exception $exception) {
            $db->rollback();
            $message = 'Error deleting permission: ' . $exception->getMessage();
            $message_type = 'error';
        }
    }
}


// --- Fetch Data for Display ---
$permissions = $db->query("SELECT * FROM permissions ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

?>

<?php if ($message): ?>
<div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="content-block">
    <h2>Manage Permissions</h2>
    <p>This is the master list of all permissions available in the system. After adding a permission here, you can assign it to roles on the <a href="index.php?page=roles">Roles page</a>.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($permissions as $perm): ?>
            <tr>
                <td><?php echo $perm['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($perm['name']); ?></strong></td>
                <td><?php echo htmlspecialchars($perm['description']); ?></td>
                <td>
                    <form action="index.php?page=permissions" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this permission?');">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="delete_permission">
                        <input type="hidden" name="perm_id" value="<?php echo $perm['id']; ?>">
                        <button type="submit" class="btn btn-accent btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="content-block">
    <h2>Add New Permission</h2>
    <form action="index.php?page=permissions" method="post">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="add_permission">
        <div class="form-group">
            <label for="name">Permission Name (e.g., "manage_pages")</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" id="description" name="description">
        </div>
        <button type="submit" class="btn btn-secondary">Add Permission</button>
    </form>
</div>

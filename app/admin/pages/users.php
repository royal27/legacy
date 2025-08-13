<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

// Security Check
if (!user_has_permission('manage_users')) {
    echo '<div class="message-box error">You do not have permission to manage users.</div>';
    return;
}

$message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);


// --- Handle user deletion ---
if (isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    validate_csrf_token();
    $user_id_to_delete = (int)$_POST['user_id'];
    // You can't delete yourself or the main admin (user ID 1)
    if ($user_id_to_delete === $_SESSION['user_id']) {
        $message = ['type' => 'error', 'text' => 'You cannot delete yourself.'];
    } elseif ($user_id_to_delete === 1) {
        $message = ['type' => 'error', 'text' => 'You cannot delete the main administrator account.'];
    } else {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id_to_delete);
        if ($stmt->execute()) {
            $message = ['type' => 'success', 'text' => 'User deleted successfully.'];
        } else {
            $message = ['type' => 'error', 'text' => 'Error deleting user.'];
        }
        $stmt->close();
    }
}


// Fetch all users with their role names
$users_res = $db->query(
    "SELECT u.*, r.name as role_name
     FROM users u
     LEFT JOIN roles r ON u.role_id = r.id
     ORDER BY u.id ASC"
);
$users = $users_res->fetch_all(MYSQLI_ASSOC);

?>

<?php if ($message): ?>
<div class="message-box <?php echo $message['type']; ?>"><?php echo $message['text']; ?></div>
<?php endif; ?>

<div class="content-block">
    <h2>Manage Users</h2>
    <p>From here you can edit and manage all registered users on the website.</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                <td>
                    <?php if ($user['is_banned']): ?>
                        <span class="status-badge error">Banned</span>
                    <?php elseif (!$user['is_validated']): ?>
                        <span class="status-badge warning">Not Validated</span>
                    <?php else: ?>
                        <span class="status-badge success">Active</span>
                    <?php endif; ?>
                    <?php if ($user['is_muted']): ?>
                        <span class="status-badge info">Muted</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?page=edit_user&id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                    <form action="index.php?page=users" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this user?');">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" class="btn btn-accent btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.status-badge.warning { background-color: #ffc107; }
.status-badge.error { background-color: var(--color-accent); }
.status-badge.info { background-color: #17a2b8; }
</style>

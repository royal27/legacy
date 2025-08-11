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

// Get user ID from URL
$user_id = (int)($_GET['id'] ?? 0);
if ($user_id === 0) {
    redirect('index.php?page=users');
}

// --- Handle form submission for updating the user ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {
    validate_csrf_token();
    $email = trim($_POST['email']);
    $role_id = (int)$_POST['role_id'];
    $is_validated = isset($_POST['is_validated']) ? 1 : 0;
    $is_banned = isset($_POST['is_banned']) ? 1 : 0;
    $is_muted = isset($_POST['is_muted']) ? 1 : 0;
    $password = $_POST['password'];

    // Prevent changing role of user 1 or banning user 1
    if ($user_id === 1) {
        $role_id = 1; // Cannot change role of super admin
        $is_banned = 0; // Cannot ban super admin
    }

    $sql = "UPDATE users SET email = ?, role_id = ?, is_validated = ?, is_banned = ?, is_muted = ?";
    $params = ['siiiii', $email, $role_id, $is_validated, $is_banned, $is_muted];

    // Handle password change
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params[0] .= 's';
        $params[] = $hashed_password;
    }

    $sql .= " WHERE id = ?";
    $params[0] .= 'i';
    $params[] = $user_id;

    $stmt = $db->prepare($sql);
    $stmt->bind_param(...$params);

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'User updated successfully.'];
    } else {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Error updating user: ' . $stmt->error];
    }
    $stmt->close();
    redirect('index.php?page=users');
}


// Fetch user data for the form
$user_res = $db->prepare("SELECT * FROM users WHERE id = ?");
$user_res->bind_param('i', $user_id);
$user_res->execute();
$user = $user_res->get_result()->fetch_assoc();

if (!$user) {
    redirect('index.php?page=users');
}

// Fetch all roles for the dropdown
$roles = $db->query("SELECT * FROM roles ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

?>

<div class="content-block">
    <a href="index.php?page=users">&larr; Back to User List</a>
    <h2>Edit User: <strong><?php echo htmlspecialchars($user['username']); ?></strong></h2>

    <form action="index.php?page=edit_user&id=<?php echo $user_id; ?>" method="post">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="update_user">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            <small>Username cannot be changed.</small>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password">
            <small>Leave blank to keep the current password.</small>
        </div>

        <div class="form-group">
            <label for="role_id">Role</label>
            <select id="role_id" name="role_id" <?php if ($user['id'] === 1) echo 'disabled'; ?>>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>" <?php echo ($user['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($role['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($user['id'] === 1): ?>
                <small>The Super Admin role cannot be changed.</small>
            <?php endif; ?>
        </div>

        <hr>
        <h3>Status Flags</h3>
        <div class="form-group-checkbox">
            <label>
                <input type="checkbox" name="is_validated" value="1" <?php echo $user['is_validated'] ? 'checked' : ''; ?>>
                User is Validated
            </label>
            <small>Un-checking this may prevent the user from logging in or accessing certain features.</small>
        </div>
         <div class="form-group-checkbox">
            <label>
                <input type="checkbox" name="is_muted" value="1" <?php echo $user['is_muted'] ? 'checked' : ''; ?>>
                Mute User
            </label>
            <small>A muted user cannot post in chats or forums (feature to be implemented by plugins).</small>
        </div>
        <div class="form-group-checkbox">
            <label>
                <input type="checkbox" name="is_banned" value="1" <?php echo $user['is_banned'] ? 'checked' : ''; ?> <?php if ($user['id'] === 1) echo 'disabled'; ?>>
                Ban User
            </label>
            <small>A banned user cannot log in. The Super Admin cannot be banned.</small>
        </div>

        <br>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <button type="button" id="kick-user-btn" class="btn btn-accent" data-user-id="<?php echo $user_id; ?>" <?php if ($user['id'] === $_SESSION['user_id']) echo 'disabled'; ?>>Kick User (Force Logout)</button>
    </form>
</div>

<style>
.form-group-checkbox { margin-bottom: 15px; }
.form-group-checkbox label { display: flex; align-items: center; }
.form-group-checkbox input { margin-right: 10px; width: auto; }
.form-group-checkbox small { display: block; margin-left: 28px; color: #6c757d; }
</style>

<script>
$(document).ready(function() {
    $('#kick-user-btn').on('click', function() {
        if (!confirm('Are you sure you want to kick this user? They will be forcefully logged out.')) {
            return;
        }
        var userId = $(this).data('user-id');
        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'kick_user', user_id: userId },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message || 'An error occurred.');
                }
            },
            error: function() {
                toastr.error('An unexpected error occurred.');
            }
        });
    });
});
</script>

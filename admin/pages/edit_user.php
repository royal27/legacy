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

// Fetch user data before handling POST to have it available
$user_res = $db->prepare("SELECT * FROM users WHERE id = ?");
$user_res->bind_param('i', $user_id);
$user_res->execute();
$user = $user_res->get_result()->fetch_assoc();
$user_res->close();

if (!$user) {
    redirect('index.php?page=users');
}

// --- Handle form submission for updating the user ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {
    validate_csrf_token();
    $email = trim($_POST['email']);
    $role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : $user['role_id'];
    $is_validated = isset($_POST['is_validated']) ? 1 : 0;
    $password = $_POST['password'];

    // Prevent changing role of user 1
    if ($user_id === 1) {
        $role_id = 1;
    }

    // NOTE: is_banned and is_muted are now handled by timed actions, so they are removed from this form handler.
    $sql = "UPDATE users SET email = ?, role_id = ?, is_validated = ?";
    $params = ['sii', $email, $role_id, $is_validated];

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

// Fetch all roles for the dropdown
$roles = $db->query("SELECT * FROM roles ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

?>

<div class="content-block" id="edit-user-container" data-user-id="<?php echo $user_id; ?>">
    <a href="index.php?page=users">&larr; Back to User List</a>
    <h2>Edit User: <strong><?php echo htmlspecialchars($user['username']); ?></strong></h2>

    <form action="index.php?page=edit_user&id=<?php echo $user_id; ?>" method="post">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="update_user">

        <h4>Basic Information</h4>
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
        <div class="form-group-checkbox">
            <label>
                <input type="checkbox" name="is_validated" value="1" <?php echo $user['is_validated'] ? 'checked' : ''; ?>>
                User is Validated
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
    <hr>

    <h3>Moderation Actions</h3>
    <div class="moderation-action">
        <strong>Mute Status:</strong> <?php echo is_user_muted($user) ? 'Muted until ' . ($user['muted_until'] ?? 'Forever') : 'Not Muted'; ?>
        <div class="ajax-action-form">
            <input type="datetime-local" class="until-datetime">
            <button type="button" class="btn btn-secondary btn-sm quick-action-btn" data-action="mute_until">Mute Until</button>
            <button type="button" class="btn btn-secondary btn-sm quick-action-btn" data-action="mute_indefinite">Mute Indefinitely</button>
            <button type="button" class="btn btn-info btn-sm quick-action-btn" data-action="unmute">Unmute</button>
        </div>
    </div>
    <div class="moderation-action">
         <strong>Ban Status:</strong> <?php echo ($user['is_banned'] || ($user['banned_until'] && new DateTime($user['banned_until']) > new DateTime())) ? 'Banned until ' . ($user['banned_until'] ?? 'Forever') : 'Not Banned'; ?>
        <div class="ajax-action-form">
            <input type="datetime-local" class="until-datetime">
            <button type="button" class="btn btn-accent btn-sm quick-action-btn" data-action="ban_until">Ban Until</button>
            <button type="button" class="btn btn-accent btn-sm quick-action-btn" data-action="ban_indefinite">Ban Indefinitely</button>
            <button type="button" class="btn btn-info btn-sm quick-action-btn" data-action="unban">Unban</button>
        </div>
    </div>
    <div class="moderation-action">
         <strong>Suspend Status:</strong> <?php echo ($user['suspended_until'] && new DateTime($user['suspended_until']) > new DateTime()) ? 'Suspended until ' . $user['suspended_until'] : 'Not Suspended'; ?>
        <div class="ajax-action-form">
            <input type="datetime-local" class="until-datetime">
            <button type="button" class="btn btn-warning btn-sm quick-action-btn" data-action="suspend_user">Suspend Until</button>
            <button type="button" class="btn btn-info btn-sm quick-action-btn" data-action="unsuspend">Unsuspend</button>
        </div>
    </div>
    <hr>
    <strong>Other Actions:</strong>
    <button type="button" id="kick-user-btn" class="btn btn-accent quick-action-btn" data-action="kick_user" <?php if ($user['id'] === $_SESSION['user_id']) echo 'disabled'; ?>>Kick User (Force Logout)</button>
</div>

<style>
.form-group-checkbox { margin-bottom: 15px; }
.form-group-checkbox label { display: flex; align-items: center; }
.form-group-checkbox input { margin-right: 10px; width: auto; }
.moderation-action { border: 1px solid #eee; padding: 15px; margin-top: 15px; border-radius: 5px; }
.moderation-action .ajax-action-form { margin-top: 10px; display: flex; gap: 10px; align-items: center; }
.btn-warning { background-color: #ffc107; color: #212529; border-color: #ffc107; }
.btn-info { background-color: #17a2b8; color: white; border-color: #17a2b8; }
</style>

<script>
$(document).ready(function() {
    function handleModerationAction(userId, subAction, untilValue = null) {
        if (!confirm('Are you sure you want to perform this action?')) {
            return;
        }
        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'moderate_user',
                user_id: userId,
                sub_action: subAction,
                until: untilValue
            },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    toastr.error(response.message || 'An error occurred.');
                }
            },
            error: function() {
                toastr.error('An unexpected error occurred.');
            }
        });
    }

    $('.quick-action-btn').on('click', function() {
        var button = $(this);
        var subAction = button.data('action');
        var userId = $('#edit-user-container').data('user-id');
        var untilValue = null;

        // If the action requires a date, get it from the sibling input
        if (subAction.includes('_until') || subAction === 'suspend_user') {
            untilValue = button.siblings('.until-datetime').val();
            if (!untilValue) {
                toastr.error('Please select a date and time.');
                return;
            }
        }

        handleModerationAction(userId, subAction, untilValue);
    });
});
</script>

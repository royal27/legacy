<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

// Security Check
if (!user_has_permission('manage_roles')) {
    echo '<div class="message-box error">You do not have permission to manage roles.</div>';
    return;
}

$message = '';
$message_type = '';

// --- Handle POST Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token();
    // --- Update role permissions ---
    if (isset($_POST['action']) && $_POST['action'] === 'update_role_permissions') {
        $role_id = (int)$_POST['role_id'];
        $permission_ids = $_POST['permissions'] ?? [];

        // Start transaction
        $db->begin_transaction();
        try {
            // 1. Delete old permissions for this role
            $stmt_delete = $db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $stmt_delete->bind_param('i', $role_id);
            $stmt_delete->execute();
            $stmt_delete->close();

            // 2. Insert new permissions
            if (!empty($permission_ids)) {
                $stmt_insert = $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
                foreach ($permission_ids as $perm_id) {
                    $permission_id = (int)$perm_id;
                    $stmt_insert->bind_param('ii', $role_id, $permission_id);
                    $stmt_insert->execute();
                }
                $stmt_insert->close();
            }

            $db->commit();
            $message = 'Permissions updated successfully.';
            $message_type = 'success';
        } catch (mysqli_sql_exception $exception) {
            $db->rollback();
            $message = 'Error updating permissions: ' . $exception->getMessage();
            $message_type = 'error';
        }
    }
}


// --- Fetch data for display ---
$roles = $db->query("SELECT * FROM roles ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
$permissions = $db->query("SELECT * FROM permissions ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// For each role, get its permissions
$role_permissions = [];
$rp_res = $db->query("SELECT * FROM role_permissions");
while ($row = $rp_res->fetch_assoc()) {
    $role_permissions[$row['role_id']][] = $row['permission_id'];
}

?>

<?php if ($message): ?>
<div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="content-block">
    <h2>Manage Roles and Permissions</h2>
    <p>Assign permissions to each role. Users will inherit permissions from their assigned role.</p>
    <p><strong>Note:</strong> The 'Admin' role (ID 1) automatically has all permissions, regardless of the settings here.</p>
</div>

<?php foreach ($roles as $role): ?>
<div class="content-block">
    <h3>Role: <?php echo htmlspecialchars($role['name']); ?></h3>
    <form action="index.php?page=roles" method="post">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="update_role_permissions">
        <input type="hidden" name="role_id" value="<?php echo $role['id']; ?>">

        <div class="permissions-grid">
            <?php foreach ($permissions as $permission): ?>
                <div class="form-group-checkbox">
                    <label>
                        <?php
                        $is_checked = in_array($permission['id'], $role_permissions[$role['id']] ?? []);
                        // The Admin role checkboxes are checked and disabled to show they have all perms
                        $is_disabled = ($role['id'] === 1);
                        if ($is_disabled) $is_checked = true;
                        ?>
                        <input type="checkbox" name="permissions[]" value="<?php echo $permission['id']; ?>" <?php echo $is_checked ? 'checked' : ''; ?> <?php echo $is_disabled ? 'disabled' : ''; ?>>
                        <?php echo htmlspecialchars($permission['name']); ?>
                    </label>
                    <small><?php echo htmlspecialchars($permission['description']); ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($role['id'] !== 1): // Don't show save button for Admin role ?>
        <br>
        <button type="submit" class="btn btn-primary">Save Permissions for <?php echo htmlspecialchars($role['name']); ?></button>
        <?php endif; ?>
    </form>
</div>
<?php endforeach; ?>

<style>
.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}
</style>

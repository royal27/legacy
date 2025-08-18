<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../src/includes/csrf.php';
require_once __DIR__ . '/../../config/database.php';

$conn = db_connect();
$role_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Redirect if ID is not provided or invalid
if (!$role_id) {
    header("Location: roles.php");
    exit();
}

// --- FORM SUBMISSION LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    csrf_validate_token();
    $response = ['status' => 'error', 'errors' => []];
    header('Content-Type: application/json');

    $role_name = trim($_POST['role_name']);
    $description = trim($_POST['description']);
    $assigned_permissions = $_POST['permissions'] ?? [];

    if ($role_id <= 2) {
        $core_role_stmt = $conn->prepare("SELECT role_name FROM roles WHERE id = ?");
        $core_role_stmt->bind_param("i", $role_id);
        $core_role_stmt->execute();
        $original_role_name = $core_role_stmt->get_result()->fetch_assoc()['role_name'];
        $core_role_stmt->close();
        if ($role_name !== $original_role_name) {
            $response['errors'][] = 'Cannot change the name of core roles.';
            echo json_encode($response);
            exit();
        }
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE roles SET role_name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $role_name, $description, $role_id);
        $stmt->execute();
        $stmt->close();

        $delete_stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $delete_stmt->bind_param("i", $role_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        if (!empty($assigned_permissions)) {
            $insert_stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($assigned_permissions as $permission_id) {
                $permission_id_int = (int)$permission_id;
                $insert_stmt->bind_param("ii", $role_id, $permission_id_int);
                $insert_stmt->execute();
            }
            $insert_stmt->close();
        }
        $conn->commit();
        $response = ['status' => 'success', 'message' => 'Role updated successfully!'];
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $response['errors'][] = 'Failed to update permissions.';
    }

    echo json_encode($response);
    exit();
}

// --- DATA FETCHING ---
$role_stmt = $conn->prepare("SELECT * FROM roles WHERE id = ?");
$role_stmt->bind_param("i", $role_id);
$role_stmt->execute();
$role = $role_stmt->get_result()->fetch_assoc();
$role_stmt->close();

if (!$role) {
    header("Location: roles.php");
    exit();
}

$all_permissions = $conn->query("SELECT * FROM permissions ORDER BY permission_name")->fetch_all(MYSQLI_ASSOC);
$role_permissions_result = $conn->query("SELECT permission_id FROM role_permissions WHERE role_id = $role_id");
$assigned_permission_ids = array_column($role_permissions_result->fetch_all(MYSQLI_ASSOC), 'permission_id');
$conn->close();

csrf_generate_token();
?>

<?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>

<h1>Edit Role: <?php echo htmlspecialchars($role['role_name']); ?></h1>
<a href="roles.php" class="btn" style="margin-bottom: 1rem; background-color: #7f8c8d;">&larr; Back to Roles List</a>

<div class="content-box">
    <form action="edit_role.php?id=<?php echo $role['id']; ?>" method="POST" class="admin-form ajax-form" style="max-width:100%">
        <?php echo csrf_input(); ?>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['errors'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars(implode(', ', $_SESSION['errors'])) . '</div>';
            unset($_SESSION['errors']);
        }
        ?>
        <div class="form-group">
            <label for="role_name">Role Name</label>
            <input type="text" id="role_name" name="role_name" class="form-control" value="<?php echo htmlspecialchars($role['role_name']); ?>" required <?php if($role['id'] <= 2) echo 'readonly'; ?>>
            <?php if($role['id'] <= 2): ?><small>Core role names cannot be changed.</small><?php endif; ?>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="2"><?php echo htmlspecialchars($role['description']); ?></textarea>
        </div>

        <hr style="margin: 2rem 0;">

        <div class="form-group">
            <h3>Assign Permissions to "<?php echo htmlspecialchars($role['role_name']); ?>"</h3>
            <div class="permissions-grid">
                <?php if (empty($all_permissions)): ?>
                    <p>No permissions have been created yet. <a href="permissions.php">Create some first!</a></p>
                <?php else: ?>
                    <?php foreach ($all_permissions as $permission): ?>
                        <div class="permission-item">
                            <label>
                                <input type="checkbox" name="permissions[]" value="<?php echo $permission['id']; ?>"
                                    <?php if (in_array($permission['id'], $assigned_permission_ids)) echo 'checked'; ?>>
                                <strong><?php echo htmlspecialchars($permission['permission_name']); ?></strong>
                                <small>(<?php echo htmlspecialchars($permission['description']); ?>)</small>
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" name="update_role" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../templates/admin/footer.php'; ?>

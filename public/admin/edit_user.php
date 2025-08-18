<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../src/includes/csrf.php';
require_once __DIR__ . '/../../config/database.php';

$conn = db_connect();
$user_id_to_edit = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$user_id_to_edit) {
    header("Location: users.php");
    exit();
}

// --- FORM SUBMISSION LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user_roles'])) {
    csrf_validate_token();
    $response = ['status' => 'error', 'errors' => []];
    header('Content-Type: application/json');

    $assigned_roles = $_POST['roles'] ?? [];
    $current_user_id = $_SESSION['user_id'];

    if ($user_id_to_edit == $current_user_id) {
        $admin_role_id_query = $conn->query("SELECT id FROM roles WHERE role_name = 'Admin' LIMIT 1");
        $admin_role_id = $admin_role_id_query->fetch_assoc()['id'];
        if (!in_array($admin_role_id, $assigned_roles)) {
            $response['errors'][] = 'You cannot remove your own Administrator role.';
            echo json_encode($response);
            exit();
        }
    }

    $conn->begin_transaction();
    try {
        $delete_stmt = $conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $delete_stmt->bind_param("i", $user_id_to_edit);
        $delete_stmt->execute();
        $delete_stmt->close();

        if (!empty($assigned_roles)) {
            $insert_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            foreach ($assigned_roles as $role_id) {
                $role_id_int = (int)$role_id;
                $insert_stmt->bind_param("ii", $user_id_to_edit, $role_id_int);
                $insert_stmt->execute();
            }
            $insert_stmt->close();
        }
        $conn->commit();
        $response = ['status' => 'success', 'message' => 'User roles updated successfully! Page will reload.'];
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $response['errors'][] = 'Failed to update roles.';
    }

    echo json_encode($response);
    exit();
}


// --- DATA FETCHING ---
$user_stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id_to_edit);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

if (!$user) {
    header("Location: users.php");
    exit();
}

$all_roles = $conn->query("SELECT * FROM roles ORDER BY role_name")->fetch_all(MYSQLI_ASSOC);
$user_roles_result = $conn->query("SELECT role_id FROM user_roles WHERE user_id = $user_id_to_edit");
$assigned_role_ids = array_column($user_roles_result->fetch_all(MYSQLI_ASSOC), 'role_id');
$conn->close();

csrf_generate_token();
?>

<?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>

<h1>Edit User: <?php echo htmlspecialchars($user['username']); ?></h1>
<a href="users.php" class="btn" style="margin-bottom: 1rem; background-color: #7f8c8d;">&larr; Back to Users List</a>

<div class="content-box">
    <form action="edit_user.php?id=<?php echo $user['id']; ?>" method="POST" class="admin-form ajax-form" style="max-width:100%">
        <?php echo csrf_input(); ?>
        <?php
        if (isset($_SESSION['errors'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars(implode(', ', $_SESSION['errors'])) . '</div>';
            unset($_SESSION['errors']);
        }
        ?>
        <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
        </div>

        <hr style="margin: 2rem 0;">

        <div class="form-group">
            <h3>Assign Roles to "<?php echo htmlspecialchars($user['username']); ?>"</h3>
            <div class="permissions-grid"> <!-- Re-using the same style -->
                <?php foreach ($all_roles as $role): ?>
                    <div class="permission-item">
                        <label>
                            <input type="checkbox" name="roles[]" value="<?php echo $role['id']; ?>"
                                <?php if (in_array($role['id'], $assigned_role_ids)) echo 'checked'; ?>>
                            <strong><?php echo htmlspecialchars($role['role_name']); ?></strong>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" name="update_user_roles" class="btn btn-primary">Save Role Assignments</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../templates/admin/footer.php'; ?>

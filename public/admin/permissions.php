<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../src/includes/csrf.php';
require_once __DIR__ . '/../../config/database.php';

$conn = db_connect();

// --- FORM SUBMISSION LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_token();
    // --- Add Permission ---
    if (isset($_POST['add_permission'])) {
        $permission_name = trim($_POST['permission_name']);
        $description = trim($_POST['description']);

        if (empty($permission_name)) {
            $_SESSION['errors'] = ['Permission name is required.'];
        } elseif (!preg_match('/^[a-z0-9_]+$/', $permission_name)) {
            $_SESSION['errors'] = ['Permission name can only contain lowercase letters, numbers, and underscores.'];
        } else {
            // Check if permission already exists
            $stmt = $conn->prepare("SELECT id FROM permissions WHERE permission_name = ?");
            $stmt->bind_param("s", $permission_name);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $_SESSION['errors'] = ['This permission name already exists.'];
            } else {
                $insert_stmt = $conn->prepare("INSERT INTO permissions (permission_name, description) VALUES (?, ?)");
                $insert_stmt->bind_param("ss", $permission_name, $description);
                if ($insert_stmt->execute()) {
                    $_SESSION['success_message'] = 'Permission added successfully!';
                } else {
                    $_SESSION['errors'] = ['Failed to add permission.'];
                }
                $insert_stmt->close();
            }
            $stmt->close();
        }
        header("Location: permissions.php");
        exit();
    }

    // --- Delete Permission ---
    if (isset($_POST['delete_permission'])) {
        $permission_id = (int)$_POST['permission_id'];

        // Use a transaction to ensure atomicity
        $conn->begin_transaction();
        try {
            // Delete from pivot tables first
            $stmt1 = $conn->prepare("DELETE FROM role_permissions WHERE permission_id = ?");
            $stmt1->bind_param("i", $permission_id);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $conn->prepare("DELETE FROM menu_permissions WHERE permission_id = ?");
            $stmt2->bind_param("i", $permission_id);
            $stmt2->execute();
            $stmt2->close();

            // Delete the permission itself
            $stmt3 = $conn->prepare("DELETE FROM permissions WHERE id = ?");
            $stmt3->bind_param("i", $permission_id);
            $stmt3->execute();
            $stmt3->close();

            $conn->commit();
            $_SESSION['success_message'] = 'Permission deleted successfully!';
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $_SESSION['errors'] = ['Failed to delete permission.'];
        }

        header("Location: permissions.php");
        exit();
    }
}

// --- DATA FETCHING for display ---
$result = $conn->query("SELECT * FROM permissions ORDER BY permission_name ASC");
$permissions = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();

csrf_generate_token();
?>

<?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>

<h1>Role & Permission Management</h1>

<div class="content-box">
    <h2>Add New Permission</h2>
    <p>Permissions are specific rights that can be assigned to roles (e.g., 'manage_users', 'edit_posts').</p>
    <form action="permissions.php" method="POST" class="admin-form" style="max-width: 100%;">
        <?php echo csrf_input(); ?>
        <?php
        // Display messages
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
            <label for="permission_name">Permission Name (no spaces, e.g., manage_users)</label>
            <input type="text" id="permission_name" name="permission_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="2"></textarea>
        </div>
        <button type="submit" name="add_permission" class="btn btn-primary">Add Permission</button>
    </form>
</div>

<div class="content-box" style="margin-top: 2rem;">
    <h2>Existing Permissions</h2>
    <table class="content-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Permission Name</th>
                <th>Description</th>
                <th style="width: 150px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($permissions)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No permissions found. Add one above to get started.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($permissions as $permission): ?>
                    <tr>
                        <td><?php echo $permission['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($permission['permission_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($permission['description']); ?></td>
                        <td>
                            <!-- Delete form -->
                            <form action="permissions.php" method="POST" style="display:inline;">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="permission_id" value="<?php echo $permission['id']; ?>">
                                <button type="submit" name="delete_permission" class="btn btn-danger" onclick="return confirm('Are you sure? This will remove the permission from all roles.')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../templates/admin/footer.php'; ?>

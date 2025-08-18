<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../src/includes/csrf.php';
require_once __DIR__ . '/../../config/database.php';

$conn = db_connect();

// --- FORM SUBMISSION LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_token();
    // Add Role
    if (isset($_POST['add_role'])) {
        $role_name = trim($_POST['role_name']);
        $description = trim($_POST['description']);
        if (!empty($role_name)) {
            $stmt = $conn->prepare("INSERT INTO roles (role_name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $role_name, $description);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Role added successfully.';
            } else {
                $_SESSION['errors'] = ['Failed to add role. It might already exist.'];
            }
            $stmt->close();
        } else {
            $_SESSION['errors'] = ['Role name is required.'];
        }
        header("Location: roles.php");
        exit();
    }

    // Delete Role
    if (isset($_POST['delete_role'])) {
        $role_id = (int)$_POST['role_id'];
        // Prevent deletion of core roles 'Admin' and 'User' by ID
        if ($role_id > 2) {
            $stmt = $conn->prepare("DELETE FROM roles WHERE id = ?");
            $stmt->bind_param("i", $role_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Role deleted successfully.';
            } else {
                $_SESSION['errors'] = ['Failed to delete role.'];
            }
            $stmt->close();
        } else {
            $_SESSION['errors'] = ['Cannot delete core system roles.'];
        }
        header("Location: roles.php");
        exit();
    }
}

// --- DATA FETCHING ---
$roles = $conn->query("SELECT * FROM roles ORDER BY role_name ASC")->fetch_all(MYSQLI_ASSOC);
$conn->close();

csrf_generate_token();
?>

<?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>

<h1>Roles Management</h1>
<p>Create and manage user roles. To assign permissions to a role, click "Edit". The 'Admin' and 'User' roles cannot be deleted.</p>

<div class="content-box">
    <h3>Add New Role</h3>
    <form action="roles.php" method="POST" class="admin-form" style="max-width: 100%;">
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
            <input type="text" id="role_name" name="role_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="2"></textarea>
        </div>
        <button type="submit" name="add_role" class="btn btn-primary">Add Role</button>
    </form>
</div>

<div class="content-box" style="margin-top: 2rem;">
    <h3>Existing Roles</h3>
    <table class="content-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Role Name</th>
                <th>Description</th>
                <th style="width: 200px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roles as $role): ?>
                <tr>
                    <td><?php echo $role['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($role['role_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($role['description']); ?></td>
                    <td>
                        <a href="edit_role.php?id=<?php echo $role['id']; ?>" class="btn btn-primary">Edit Permissions</a>
                        <?php if ($role['id'] > 2): // Cannot delete Admin and User roles ?>
                        <form action="roles.php" method="POST" style="display:inline;">
                            <?php echo csrf_input(); ?>
                            <input type="hidden" name="role_id" value="<?php echo $role['id']; ?>">
                            <button type="submit" name="delete_role" class="btn btn-danger" onclick="return confirm('Are you sure? This will not unassign users from this role.')">Delete</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../templates/admin/footer.php'; ?>

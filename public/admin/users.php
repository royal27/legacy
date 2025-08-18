<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../config/database.php';

$conn = db_connect();
// This query fetches all users and concatenates their assigned roles into a single string.
$sql = "SELECT u.id, u.username, u.email, u.created_at, GROUP_CONCAT(r.role_name ORDER BY r.role_name SEPARATOR ', ') as roles
        FROM users u
        LEFT JOIN user_roles ur ON u.id = ur.user_id
        LEFT JOIN roles r ON ur.role_id = r.id
        GROUP BY u.id
        ORDER BY u.created_at DESC";
$users = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>

<h1>User Management</h1>
<p>View user accounts and manage their roles by clicking "Edit".</p>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}
?>

<div class="content-box">
    <h3>All Users</h3>
    <div style="overflow-x: auto;">
        <table class="content-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Member Since</th>
                    <th style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo !empty($user['roles']) ? htmlspecialchars($user['roles']) : '<span style="color:#999;">None</span>'; ?></td>
                            <td><?php echo date('M j, Y, g:i A', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">Edit Roles</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../templates/admin/footer.php'; ?>

<div class="card">
    <div class="page-header">
        <h1>User Management</h1>
        <a href="<?= url('admin/users/new') ?>" class="btn">Add New User</a>
    </div>
    <p>Here you can view and manage all registered users.</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($users) && !empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="role-badge"><?= htmlspecialchars($user['role_name']) ?></span></td>
                        <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                        <td>
                            <a href="<?= url('admin/users/edit/' . $user['id']) ?>" class="btn-action edit">Edit</a>
                            <a href="<?= url('admin/users/delete/' . $user['id']) ?>" class="btn-action delete" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php \App\Core\Hooks::trigger('admin_user_actions', $user); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.page-header h1 {
    margin: 0;
}
.notice {
    padding: 15px;
    border-radius: 8px;
    border: 1px solid;
    margin-bottom: 20px;
}
.notice.success {
    background-color: #2be28930;
    border-color: #2be289;
    color: #fff;
}
.notice.error {
    background-color: #ff450030;
    border-color: #ff4500;
    color: #fff;
}
/* Scoped styles for this view, can be moved to a main admin CSS file later */
.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
.data-table th, .data-table td {
    padding: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    text-align: left;
}
.data-table th {
    background-color: rgba(0, 0, 0, 0.2);
}
.data-table tr:hover {
    background-color: rgba(0, 0, 0, 0.1);
}
.role-badge {
    padding: 4px 8px;
    border-radius: 6px;
    background-color: var(--primary-color);
    color: #fff;
    font-size: 0.9em;
    font-weight: 500;
}
.btn-action {
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    font-size: 0.9em;
    margin-right: 5px;
}
.btn-action.edit {
    background-color: var(--secondary-color);
}
.btn-action.delete {
    background-color: var(--accent-color);
}
</style>

<div class="card">
    <div class="page-header">
        <h1>Role Management</h1>
        <a href="/admin/roles/new" class="btn">Add New Role</a>
    </div>
    <p>Here you can manage user roles and their permissions.</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Role Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($roles) && !empty($roles)): ?>
                <?php foreach ($roles as $role): ?>
                    <tr>
                        <td><?= htmlspecialchars($role['id']) ?></td>
                        <td><?= htmlspecialchars($role['name']) ?></td>
                        <td>
                            <a href="/admin/roles/edit/<?= $role['id'] ?>" class="btn-action edit">Edit Permissions</a>
                            <?php if ($role['id'] > 2): // Prevent deleting Founder/User roles ?>
                                <a href="/admin/roles/delete/<?= $role['id'] ?>" class="btn-action delete" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No roles found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
/* Using styles from user management for consistency */
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

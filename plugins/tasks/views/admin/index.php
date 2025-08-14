<div class="card">
    <div class="page-header">
        <h1>Task Management</h1>
        <a href="/admin/tasks/new" class="btn">Add New Task</a>
    </div>
    <p>Assign and manage tasks for your team.</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>Task</th>
                <th>Assigned To</th>
                <th>Created By</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tasks)): ?>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= htmlspecialchars($task['title']) ?></td>
                        <td><?= htmlspecialchars($task['assigned_to_name']) ?></td>
                        <td><?= htmlspecialchars($task['creator_name']) ?></td>
                        <td><span class="status-badge status-<?= strtolower(str_replace(' ', '-', $task['status'])) ?>"><?= htmlspecialchars($task['status']) ?></span></td>
                        <td><?= $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : 'N/A' ?></td>
                        <td>
                            <a href="/admin/tasks/edit/<?= $task['id'] ?>" class="btn-action edit">Edit</a>
                            <a href="/admin/tasks/delete/<?= $task['id'] ?>" class="btn-action delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No tasks found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
/* Reusing styles for consistency */
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.page-header h1 { margin: 0; }
.data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.data-table th, .data-table td { padding: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); text-align: left; }
.data-table th { background-color: rgba(0, 0, 0, 0.2); }
.btn-action.edit { background-color: var(--secondary-color); color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
.btn-action.delete { background-color: var(--accent-color); color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
.notice.success { padding: 15px; border-radius: 8px; border: 1px solid; margin-bottom: 20px; background-color: #2be28930; border-color: #2be289; color: #fff; }
.status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; color: #fff; }
.status-to-do { background-color: #e74c3c; }
.status-in-progress { background-color: #f1c40f; }
.status-done { background-color: #2ecc71; }
</style>

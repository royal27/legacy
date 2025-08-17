<a href="/admin/downloads"> &laquo; Back to Downloads Dashboard</a>
<div class="card">
    <div class="page-header">
        <h1>Validate Submitted Files</h1>
    </div>
    <p>Review and approve files submitted by users.</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>File</th>
                <th>User</th>
                <th>Category</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($files)): ?>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($file['title']) ?></strong> (<?= htmlspecialchars($file['filename']) ?>)
                            <p><?= htmlspecialchars($file['description']) ?></p>
                        </td>
                        <td><?= htmlspecialchars($file['username']) ?></td>
                        <td><?= htmlspecialchars($file['category_name']) ?></td>
                        <td><?= date('Y-m-d', strtotime($file['created_at'])) ?></td>
                        <td>
                            <a href="/admin/downloads/files/validate/<?= $file['id'] ?>" class="btn-action edit">Validate</a>
                            <a href="/admin/downloads/files/delete/<?= $file['id'] ?>" class="btn-action delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No files are awaiting validation.</td>
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
.data-table th, .data-table td { padding: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); text-align: left; vertical-align: top; }
.data-table th { background-color: rgba(0, 0, 0, 0.2); }
.data-table p { font-size: 0.9em; color: rgba(255,255,255,0.7); margin: 5px 0 0 0; }
.btn-action.edit { background-color: #2ecc71; color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
.btn-action.delete { background-color: var(--accent-color); color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
.notice.success { padding: 15px; border-radius: 8px; border: 1px solid; margin-bottom: 20px; background-color: #2be28930; border-color: #2be289; color: #fff; }
</style>

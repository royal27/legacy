<a href="/admin/downloads"> &laquo; Back to Downloads Dashboard</a>
<div class="card">
    <div class="page-header">
        <h1>Manage Categories</h1>
        <a href="/admin/downloads/categories/new" class="btn">Add New Category</a>
    </div>
    <p>Here you can manage download categories.</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= $category['id'] ?></td>
                        <td><?= htmlspecialchars($category['name']) ?></td>
                        <td><?= htmlspecialchars($category['description']) ?></td>
                        <td>
                            <a href="/admin/downloads/categories/edit/<?= $category['id'] ?>" class="btn-action edit">Edit</a>
                            <a href="/admin/downloads/categories/delete/<?= $category['id'] ?>" class="btn-action delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No categories found.</td>
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
</style>

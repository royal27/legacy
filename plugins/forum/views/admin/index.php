<div class="card">
    <div class="page-header">
        <h1>Manage Forums</h1>
        <a href="/admin/forum/new" class="btn">Add New Category/Forum</a>
    </div>
    <p>Here you can create, edit, and delete forum categories and forums.</p>

    <div class="forum-admin-list">
        <?php if (isset($forums) && !empty($forums)): ?>
            <?php foreach ($forums as $category): ?>
                <div class="category-manage-item">
                    <div class="item-info">
                        <strong><?= htmlspecialchars($category['name']) ?></strong>
                        <p><?= htmlspecialchars($category['description']) ?></p>
                    </div>
                    <div class="item-actions">
                        <a href="/admin/forum/edit/<?= $category['id'] ?>" class="btn-action edit">Edit</a>
                        <a href="/admin/forum/delete/<?= $category['id'] ?>" class="btn-action delete" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
                <?php if (!empty($category['subforums'])): ?>
                    <div class="subforum-manage-list">
                        <?php foreach ($category['subforums'] as $forum): ?>
                             <div class="subforum-manage-item">
                                <div class="item-info">
                                    <strong><?= htmlspecialchars($forum['name']) ?></strong>
                                    <p><?= htmlspecialchars($forum['description']) ?></p>
                                </div>
                                <div class="item-actions">
                                    <a href="/admin/forum/edit/<?= $forum['id'] ?>" class="btn-action edit">Edit</a>
                                    <a href="/admin/forum/delete/<?= $forum['id'] ?>" class="btn-action delete" onclick="return confirm('Are you sure?')">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No forums have been created yet.</p>
        <?php endif; ?>
    </div>
</div>
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.page-header h1 { margin: 0; }
.category-manage-item, .subforum-manage-item { display: flex; align-items: center; padding: 15px; border: 1px solid rgba(255,255,255,0.2); }
.category-manage-item { background-color: rgba(0,0,0,0.2); border-radius: 8px 8px 0 0; }
.subforum-manage-list { margin-left: 30px; border-left: 1px solid rgba(255,255,255,0.2); border-right: 1px solid rgba(255,255,255,0.2); }
.subforum-manage-item { border-top: none; }
.item-info { flex: 1; }
.item-info p { margin: 5px 0 0 0; font-size: 0.9em; color: rgba(255,255,255,0.7); }
.btn-action { padding: 5px 10px; border-radius: 5px; text-decoration: none; color: white; font-size: 0.9em; margin-left: 5px; }
.btn-action.edit { background-color: var(--secondary-color); }
.btn-action.delete { background-color: var(--accent-color); }
</style>

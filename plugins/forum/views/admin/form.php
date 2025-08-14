<?php
// Determine if we are editing or creating
$is_edit = isset($forum);
$action_url = $is_edit ? "/admin/forum/update/{$forum['id']}" : "/admin/forum/create";
?>
<div class="card">
    <h1><?= htmlspecialchars($title) ?></h1>

    <form action="<?= $action_url ?>" method="POST">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($forum['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"><?= htmlspecialchars($forum['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="parent_id">Parent Category</label>
            <select name="parent_id" id="parent_id">
                <option value="">-- None (This is a top-level category) --</option>
                <?php foreach ($forums as $category): ?>
                    <?php if (!$is_edit || $category['id'] != $forum['id']): // Prevent a category from being its own parent ?>
                        <option value="<?= $category['id'] ?>" <?= (($is_edit && $forum['parent_id'] == $category['id']) ? 'selected' : '') ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="sort_order">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" value="<?= htmlspecialchars($forum['sort_order'] ?? '0') ?>" required>
        </div>

        <button type="submit" class="btn"><?= $is_edit ? 'Save Changes' : 'Create Forum' ?></button>
        <a href="/admin/forum" class="btn-link">Cancel</a>
    </form>
</div>

<style>
/* Reusing styles for consistency */
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
input[type="text"], input[type="number"], textarea, select {
    width: 100%;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    box-sizing: border-box;
}
.btn { display: inline-block; padding: 12px 20px; background: var(--primary-color); border: none; border-radius: 8px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
.btn-link { display: inline-block; margin-left: 10px; color: var(--accent-color); }
</style>

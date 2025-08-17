<?php
$is_edit = isset($category);
$action_url = $is_edit ? "/admin/downloads/categories/update/{$category['id']}" : "/admin/downloads/categories/create";
?>
<div class="card">
    <h1><?= htmlspecialchars($title) ?></h1>

    <form action="<?= $action_url ?>" method="POST">
        <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn"><?= $is_edit ? 'Save Changes' : 'Create Category' ?></button>
        <a href="/admin/downloads/categories" class="btn-link">Cancel</a>
    </form>
</div>

<style>
/* Reusing styles for consistency */
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
input[type="text"], textarea {
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

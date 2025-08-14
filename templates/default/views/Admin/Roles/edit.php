<div class="card">
    <h1>Edit Role: <?= htmlspecialchars($role['name']) ?></h1>
    <p>Assign permissions to this role.</p>

    <form action="/admin/roles/update/<?= $role['id'] ?>" method="POST">
        <fieldset>
            <legend>Permissions</legend>
            <div class="permissions-grid">
                <?php foreach ($all_permissions as $permission): ?>
                    <div class="permission-item">
                        <input type="checkbox"
                               name="permissions[]"
                               value="<?= $permission['id'] ?>"
                               id="perm_<?= $permission['id'] ?>"
                               <?php if (in_array($permission['id'], $role_permissions)): ?>checked<?php endif; ?>
                               <?php if ($role['id'] == 1): ?>disabled<?php endif; // Disable for Founder role ?>
                        >
                        <label for="perm_<?= $permission['id'] ?>">
                            <strong><?= htmlspecialchars($permission['permission_key']) ?></strong><br>
                            <small><?= htmlspecialchars($permission['description']) ?></small>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <?php if ($role['id'] == 1): ?>
            <p><em>The Founder role has all permissions by default and cannot be changed.</em></p>
        <?php else: ?>
            <button type="submit" class="btn">Save Permissions</button>
        <?php endif; ?>
        <a href="/admin/roles" class="btn-link">Cancel</a>
    </form>
</div>

<style>
fieldset {
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}
legend {
    padding: 0 10px;
    font-weight: 500;
}
.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
}
.permission-item {
    display: flex;
    align-items: flex-start;
    background: rgba(0,0,0,0.2);
    padding: 10px;
    border-radius: 6px;
}
.permission-item input[type="checkbox"] {
    margin-right: 10px;
    margin-top: 5px;
    width: auto;
}
.permission-item label {
    font-size: 14px;
}
.permission-item small {
    color: rgba(255, 255, 255, 0.6);
}
.btn {
    display: inline-block;
    padding: 12px 20px;
    background: var(--primary-color);
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
}
.btn-link {
    display: inline-block;
    margin-left: 10px;
    color: var(--accent-color);
}
</style>

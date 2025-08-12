<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p>Edit the role name and assign permissions.</p>

<form action="/admin/roles/edit/<?php echo $data['role']['id']; ?>" method="post" style="max-width: 500px;">
    <label for="name">Role Name: <sup>*</sup></label>
    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($data['role']['name']); ?>" required <?php if ($data['role']['id'] <= 2) echo 'readonly'; ?>>

    <fieldset style="margin-top: 1rem; border: 1px solid var(--border-color); padding: 1rem;">
        <legend>Permissions</legend>
        <?php if ($data['role']['id'] == 1): ?>
            <p>The Founder role has all permissions by default.</p>
        <?php else: ?>
            <?php foreach ($data['permissions'] as $label => $key): ?>
                <?php $checked = isset($data['role_permissions'][$key]) && $data['role_permissions'][$key] ? 'checked' : ''; ?>
                <div style="margin-bottom: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="<?php echo $key; ?>" id="perm_<?php echo $key; ?>" <?php echo $checked; ?>>
                    <label for="perm_<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></label>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </fieldset>

    <button type="submit" class="btn" style="margin-top: 1rem;">Update Role</button>
    <a href="/admin/roles" style="margin-left: 1rem;">Cancel</a>
</form>

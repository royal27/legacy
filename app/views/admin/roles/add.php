<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p>Create a new role and assign permissions.</p>

<form action="/admin/roles/add" method="post" style="max-width: 500px;">
    <label for="name">Role Name: <sup>*</sup></label>
    <input type="text" name="name" id="name" required>

    <fieldset style="margin-top: 1rem; border: 1px solid var(--border-color); padding: 1rem;">
        <legend>Permissions</legend>
        <?php foreach ($data['permissions'] as $label => $key): ?>
            <div style="margin-bottom: 0.5rem;">
                <input type="checkbox" name="permissions[]" value="<?php echo $key; ?>" id="perm_<?php echo $key; ?>">
                <label for="perm_<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></label>
            </div>
        <?php endforeach; ?>
    </fieldset>

    <button type="submit" class="btn" style="margin-top: 1rem;">Create Role</button>
    <a href="/admin/roles" style="margin-left: 1rem;">Cancel</a>
</form>

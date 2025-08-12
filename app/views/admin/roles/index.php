<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
    <a href="/admin/roles/add" class="btn">Add New Role</a>
</div>

<p>Here you can manage user roles and their permissions.</p>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="text-align: left;">
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color);">ID</th>
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color);">Role Name</th>
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color);">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['roles'] as $role): ?>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);"><?php echo $role['id']; ?></td>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);"><?php echo htmlspecialchars($role['name']); ?></td>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);">
                    <a href="/admin/roles/edit/<?php echo $role['id']; ?>" class="btn">Edit</a>
                    <?php if ($role['id'] > 2): // Prevent showing delete button for Founder and Member roles ?>
                        <a href="/admin/roles/delete/<?php echo $role['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this role?');" style="background: var(--color-accent);">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
    <a href="/admin/users/add" class="btn">Add New User</a>
</div>

<p>Here you can manage all registered users.</p>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="text-align: left;">
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color);">ID</th>
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color);">Username</th>
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color);">Email</th>
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color);">Role</th>
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color);">Registered</th>
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color);">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['users'] as $user): ?>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);"><?php echo $user['id']; ?></td>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);"><?php echo htmlspecialchars($user['username']); ?></td>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);"><?php echo htmlspecialchars($user['email']); ?></td>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);"><?php echo htmlspecialchars($user['role_name']); ?></td>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);"><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);">
                    <a href="/admin/users/edit/<?php echo $user['id']; ?>" class="btn">Edit</a>
                    <?php if ($user['id'] != 1): // Prevent showing delete button for Founder ?>
                        <a href="/admin/users/delete/<?php echo $user['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this user?');" style="background: var(--color-accent);">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

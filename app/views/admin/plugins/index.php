<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <div>
        <h1 class="gradient-text" style="margin: 0;"><?php echo htmlspecialchars($data['title']); ?></h1>
        <p style="margin: 0;">Here you can manage plugins for the site.</p>
    </div>
</div>

<div class="admin-card" style="margin-bottom: 2rem;">
    <h2>Upload New Plugin</h2>
    <p>Upload a .zip file containing the plugin folder.</p>
    <form action="/admin/plugins/upload" method="post" enctype="multipart/form-data">
        <input type="file" name="plugin_zip" accept=".zip" required>
        <button type="submit" class="btn">Upload</button>
    </form>
</div>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="text-align: left;">
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color); width: 25%;">Plugin</th>
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color); width: 55%;">Description</th>
            <th style="padding: 8px; border-bottom: 2px solid var(--border-color); width: 20%;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['plugins'] as $folder => $plugin_data): ?>
            <?php $is_active = in_array($folder, $data['active_plugins']); ?>
            <tr style="background: <?php echo $is_active ? 'rgba(65, 105, 225, 0.1)' : 'transparent'; ?>">
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);">
                    <strong><?php echo htmlspecialchars($plugin_data['Plugin Name']); ?></strong>
                    <br>
                    <small>Version: <?php echo htmlspecialchars($plugin_data['Version']); ?> | By: <?php echo htmlspecialchars($plugin_data['Author']); ?></small>
                </td>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);"><?php echo htmlspecialchars($plugin_data['Description']); ?></td>
                <td style="padding: 8px; border-bottom: 1px solid var(--border-color);">
                    <?php if ($is_active): ?>
                        <a href="/admin/plugins/deactivate/<?php echo $folder; ?>" class="btn" style="background: var(--color-accent);">Deactivate</a>
                    <?php else: ?>
                        <a href="/admin/plugins/activate/<?php echo $folder; ?>" class="btn">Activate</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

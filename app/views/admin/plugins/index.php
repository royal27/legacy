<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p>Here you can manage plugins. New plugins can be added by uploading their folder to the `/app/plugins` directory.</p>

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

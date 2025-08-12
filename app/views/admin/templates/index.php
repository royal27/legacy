<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p>Here you can manage the site's appearance by activating a template. New templates can be added by uploading their folder to the `/templates` directory.</p>

<div class="templates-grid">
    <?php foreach ($data['templates'] as $template): ?>
        <div class="template-card">
            <h3><?php echo htmlspecialchars($template['name']); ?></h3>
            <p>Folder: <code><?php echo htmlspecialchars($template['folder_name']); ?></code></p>

            <?php if ($template['is_active']): ?>
                <button class="btn" disabled>Active</button>
            <?php else: ?>
                <a href="/admin/templates/activate/<?php echo $template['id']; ?>" class="btn">Activate</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<style>
    .templates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
    .template-card {
        border: 1px solid var(--border-color);
        background: var(--bg-surface);
        padding: 1rem;
        border-radius: 5px;
        text-align: center;
    }
</style>

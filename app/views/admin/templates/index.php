<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <div>
        <h1 class="gradient-text" style="margin: 0;"><?php echo htmlspecialchars($data['title']); ?></h1>
        <p style="margin: 0;">Here you can manage the site's appearance by activating a template.</p>
    </div>
</div>

<div class="admin-card" style="margin-bottom: 2rem;">
    <h2>Upload New Template</h2>
    <p>Upload a .zip file containing the template folder.</p>
    <form action="/admin/templates/upload" method="post" enctype="multipart/form-data">
        <input type="file" name="template_zip" accept=".zip" required>
        <button type="submit" class="btn">Upload</button>
    </form>
</div>

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
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .admin-card {
        background: var(--bg-surface);
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        border-radius: 5px;
    }
</style>

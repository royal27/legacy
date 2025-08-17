<a href="<?= url('downloads') ?>"> &laquo; Back to Categories</a>
<h1><?= htmlspecialchars($title) ?></h1>

<table class="data-table">
    <thead>
        <tr>
            <th>File</th>
            <th>Uploaded by</th>
            <th>Downloads</th>
            <th>Date</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($files)): ?>
            <?php foreach ($files as $file): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($file['title']) ?></strong>
                        <p><?= htmlspecialchars($file['description']) ?></p>
                    </td>
                    <td><?= htmlspecialchars($file['author_name']) ?></td>
                    <td><?= $file['download_count'] ?></td>
                    <td><?= date('Y-m-d', strtotime($file['created_at'])) ?></td>
                    <td>
                        <a href="<?= url('downloads/go/' . $file['id']) ?>" class="btn-action view">Download</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No files in this category yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<style>
/* Reusing styles for consistency */
.data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.data-table th, .data-table td { padding: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); text-align: left; vertical-align: top; }
.data-table th { background-color: rgba(0, 0, 0, 0.2); }
.data-table p { font-size: 0.9em; color: rgba(255,255,255,0.7); margin: 5px 0 0 0; }
.btn-action.view { background-color: var(--secondary-color); color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
</style>

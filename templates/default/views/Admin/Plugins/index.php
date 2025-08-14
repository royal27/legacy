<div class="card">
    <div class="page-header">
        <h1>Plugin Management</h1>
    </div>
    <p>Activate or deactivate plugins for your site.</p>

    <?php $success = \App\Core\Session::getFlash('success'); ?>
    <?php if ($success): ?>
        <div class="notice success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php $error = \App\Core\Session::getFlash('error'); ?>
    <?php if ($error): ?>
        <div class="notice error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>Plugin</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($all_plugins) && !empty($all_plugins)): ?>
                <?php foreach ($all_plugins as $dir => $manifest): ?>
                    <?php $is_active = in_array($dir, $active_plugins); ?>
                    <tr class="<?= $is_active ? 'active' : 'inactive' ?>">
                        <td>
                            <strong><?= htmlspecialchars($manifest['name']) ?></strong><br>
                            <small>Version: <?= htmlspecialchars($manifest['version']) ?> | By: <?= htmlspecialchars($manifest['author']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($manifest['description']) ?></td>
                        <td>
                            <?php if ($is_active): ?>
                                <a href="/admin/plugins/deactivate/<?= $dir ?>" class="btn-action deactivate">Deactivate</a>
                            <?php else: ?>
                                <a href="/admin/plugins/activate/<?= $dir ?>" class="btn-action activate">Activate</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No plugins found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
/* Using styles for consistency */
.page-header h1 { margin: 0; }
.notice { padding: 15px; border-radius: 8px; border: 1px solid; margin-bottom: 20px; }
.notice.success { background-color: #2be28930; border-color: #2be289; color: #fff; }
.notice.error { background-color: #ff450030; border-color: #ff4500; color: #fff; }
.data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.data-table th, .data-table td { padding: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); text-align: left; vertical-align: top; }
.data-table th { background-color: rgba(0, 0, 0, 0.2); }
.data-table tr.inactive td { color: rgba(255,255,255,0.6); }
.data-table tr.inactive strong { color: rgba(255,255,255,0.8); }
.btn-action { padding: 5px 10px; border-radius: 5px; text-decoration: none; color: white; font-size: 0.9em; }
.btn-action.activate { background-color: var(--secondary-color); }
.btn-action.deactivate { background-color: #555; }
</style>

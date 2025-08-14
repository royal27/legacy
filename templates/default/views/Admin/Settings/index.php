<div class="card">
    <div class="page-header">
        <h1>Site Settings</h1>
    </div>
    <p>Manage general site settings and features.</p>

    <?php $success = \App\Core\Session::getFlash('success'); ?>
    <?php if ($success): ?>
        <div class="notice success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="/admin/settings/update" method="POST">
        <fieldset>
            <legend>Points System</legend>
            <div class="form-group">
                <label for="points_for_new_topic">Points for New Topic</label>
                <input type="number" name="points_for_new_topic" id="points_for_new_topic" value="<?= htmlspecialchars($settings['points_for_new_topic'] ?? '5') ?>">
            </div>
            <div class="form-group">
                <label for="points_for_new_post">Points for New Post/Reply</label>
                <input type="number" name="points_for_new_post" id="points_for_new_post" value="<?= htmlspecialchars($settings['points_for_new_post'] ?? '1') ?>">
            </div>
        </fieldset>

        <button type="submit" class="btn">Save Settings</button>
    </form>
</div>

<style>
/* Reusing styles for consistency */
.page-header h1 { margin: 0; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
input[type="number"] { width: 100%; max-width: 200px; padding: 12px; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; color: #fff; font-size: 16px; box-sizing: border-box; }
.btn { display: inline-block; padding: 12px 20px; background: var(--primary-color); border: none; border-radius: 8px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
fieldset { border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px; padding: 20px; margin: 20px 0; }
legend { padding: 0 10px; font-weight: 500; }
.notice.success { padding: 15px; border-radius: 8px; border: 1px solid; margin-bottom: 20px; background-color: #2be28930; border-color: #2be289; color: #fff; }
</style>

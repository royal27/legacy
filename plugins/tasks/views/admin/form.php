<?php
$is_edit = isset($task);
$action_url = $is_edit ? "/admin/tasks/update/{$task['id']}" : "/admin/tasks/create";
?>
<div class="card">
    <h1><?= htmlspecialchars($title) ?></h1>

    <form action="<?= $action_url ?>" method="POST">
        <div class="form-group">
            <label for="title">Task Title</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($task['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="5"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="assigned_to_user_id">Assign To</label>
            <select name="assigned_to_user_id" id="assigned_to_user_id" required>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= (($is_edit && $task['assigned_to_user_id'] == $user['id']) ? 'selected' : '') ?>>
                        <?= htmlspecialchars($user['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="To Do" <?= (($is_edit && $task['status'] == 'To Do') ? 'selected' : '') ?>>To Do</option>
                <option value="In Progress" <?= (($is_edit && $task['status'] == 'In Progress') ? 'selected' : '') ?>>In Progress</option>
                <option value="Done" <?= (($is_edit && $task['status'] == 'Done') ? 'selected' : '') ?>>Done</option>
            </select>
        </div>

        <div class="form-group">
            <label for="due_date">Due Date</label>
            <input type="date" name="due_date" id="due_date" value="<?= htmlspecialchars($task['due_date'] ?? '') ?>">
        </div>

        <button type="submit" class="btn"><?= $is_edit ? 'Save Changes' : 'Create Task' ?></button>
        <a href="/admin/tasks" class="btn-link">Cancel</a>
    </form>
</div>

<style>
/* Reusing styles for consistency */
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
input[type="text"], input[type="date"], textarea, select {
    width: 100%;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    box-sizing: border-box;
}
.btn { display: inline-block; padding: 12px 20px; background: var(--primary-color); border: none; border-radius: 8px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
.btn-link { display: inline-block; margin-left: 10px; color: var(--accent-color); }
</style>

<div class="card">
    <h1>Add New User</h1>

    <form action="<?= url('admin/users/create') ?>" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div class="form-group">
            <label for="role_id">Role</label>
            <select name="role_id" id="role_id" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>">
                        <?= htmlspecialchars($role['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn">Create User</button>
        <a href="<?= url('admin/users') ?>" class="btn-link">Cancel</a>
    </form>
</div>

<style>
/* Scoped styles for this form */
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}
input[type="text"],
input[type="email"],
input[type="password"],
select {
    width: 100%;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    box-sizing: border-box;
}
.btn {
    display: inline-block;
    padding: 12px 20px;
    background: var(--primary-color);
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
}
.btn-link {
    display: inline-block;
    margin-left: 10px;
    color: var(--accent-color);
}
</style>

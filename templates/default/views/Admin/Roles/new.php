<div class="card">
    <h1>Add New Role</h1>

    <form action="<?= url('admin/roles/create') ?>" method="POST">
        <div class="form-group">
            <label for="name">Role Name</label>
            <input type="text" name="name" id="name" required>
        </div>

        <button type="submit" class="btn">Create Role</button>
        <a href="<?= url('admin/roles') ?>" class="btn-link">Cancel</a>
    </form>
</div>

<style>
/* Reusing styles for consistency */
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}
input[type="text"] {
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

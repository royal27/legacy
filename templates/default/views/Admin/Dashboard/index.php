<div class="card">
    <h1>Admin Dashboard</h1>
    <p>Welcome to the admin panel, <?= htmlspecialchars(\App\Core\Session::get('username')) ?>!</p>
    <p>This area is protected and only accessible to logged-in users.</p>

    <nav class="admin-nav">
        <a href="/admin/users">Manage Users</a>
        <a href="/admin/roles">Manage Roles</a>
        <a href="/admin/plugins">Manage Plugins</a>
        <a href="#">Site Settings</a>
        <?php \App\Core\Hooks::trigger('admin_dashboard_nav_links'); ?>
    </nav>

    <p style="margin-top: 20px;"><a href="/logout">Logout</a></p>
</div>

<style>
.admin-nav {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}
.admin-nav a {
    display: inline-block;
    padding: 10px 15px;
    background-color: var(--secondary-color);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    margin-right: 10px;
    transition: opacity 0.3s;
}
.admin-nav a:hover {
    opacity: 0.9;
}
</style>

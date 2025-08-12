<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p>Welcome to the administration area. From here you can manage the site.</p>

<div class="admin-menu">
    <h2>Management Links</h2>
    <ul>
        <li><a href="/admin/users">Manage Users</a> (Requires 'admin.users.manage' permission)</li>
        <li><a href="/admin/roles">Manage Roles</a> (Requires 'admin.roles.manage' permission)</li>
        <li><a href="/admin/templates">Manage Templates</a> (Requires 'admin.templates.manage' permission)</li>
        <li><a href="/admin/plugins">Manage Plugins</a> (Requires 'admin.plugins.manage' permission)</li>
        <li><a href="/admin/links">Manage Navigation</a> (Requires 'admin.links.manage' permission)</li>
    </ul>
</div>

<style>
    .admin-menu ul {
        list-style: none;
        padding: 0;
    }
    .admin-menu li {
        margin-bottom: 10px;
        background: var(--bg-surface);
        padding: 15px;
        border: 1px solid var(--border-color);
        border-radius: 5px;
    }
</style>

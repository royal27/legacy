<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}
?>
<p>Welcome to the admin dashboard. From here you can manage the entire website.</p>

<div class="dashboard-widgets">
    <div class="widget">
        <h3>Site Statistics</h3>
        <ul>
            <li><strong>Total Users:</strong> [Data to be added]</li>
            <li><strong>Active Plugins:</strong> [Data to be added]</li>
            <li><strong>Footer Pages:</strong> [Data to be added]</li>
        </ul>
    </div>
    <div class="widget">
        <h3>Quick Actions</h3>
        <ul>
            <li><a href="index.php?page=settings" class="btn btn-secondary">Manage Settings</a></li>
            <li><a href="index.php?page=users" class="btn btn-primary">Manage Users</a></li>
            <li><a href="index.php?page=languages" class="btn btn-accent">Manage Languages</a></li>
        </ul>
    </div>
</div>

<style>
.dashboard-widgets {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}
.widget {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.widget h3 {
    margin-top: 0;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 15px;
}
.widget ul {
    list-style: none;
    padding: 0;
}
.widget ul li {
    margin-bottom: 10px;
}
</style>

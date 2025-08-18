<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../templates/admin/header.php';

// I will add logic here later to fetch these stats from the DB
$user_count = '--';
$role_count = '--';
$plugin_count = '--';
?>

<h1>Dashboard</h1>

<p>Welcome to the control center. Use the menu on the left to manage site content and settings.</p>

<div class="stats-container">
    <div class="stat-box">
        <h4>Total Users</h4>
        <p class="stat-number"><?php echo $user_count; ?></p>
    </div>
    <div class="stat-box">
        <h4>Total Roles</h4>
        <p class="stat-number"><?php echo $role_count; ?></p>
    </div>
    <div class="stat-box">
        <h4>Active Plugins</h4>
        <p class="stat-number"><?php echo $plugin_count; ?></p>
    </div>
    <div class="stat-box">
        <h4>PHP Version</h4>
        <p class="stat-number"><?php echo phpversion(); ?></p>
    </div>
</div>

<div class="content-box" style="margin-top: 2rem;">
    <h2>Quick Actions</h2>
    <p>Quick links to common tasks will be placed here.</p>
    <!-- Example: <a href="/admin/users.php?action=add" class="btn btn-primary">Add New User</a> -->
</div>


<?php
require_once __DIR__ . '/../../templates/admin/footer.php';
?>

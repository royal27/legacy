<?php
// 1. Protect the page
require_once __DIR__ . '/../src/includes/auth_check.php';

// 2. Load the layout
require_once __DIR__ . '/../templates/layout/header.php';
?>

<h2>User Dashboard</h2>
<p style="font-size: 1.5rem;">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>

<p>This is your personal dashboard. More features will be added soon.</p>

<!-- Placeholder for future dashboard widgets -->
<div class="dashboard-widgets">
    <div class="widget">
        <h3>Profile Settings</h3>
        <p>Here you will be able to edit your username, email, and password.</p>
        <!-- We will create this page later -->
        <a href="/user_settings.php" class="btn-widget">Manage Profile</a>
    </div>
    <div class="widget">
        <h3>My Plugins</h3>
        <p>Here you will see plugins assigned to you.</p>
        <a href="#" class="btn-widget">View Plugins</a>
    </div>
</div>

<?php
require_once __DIR__ . '/../templates/layout/footer.php';
?>

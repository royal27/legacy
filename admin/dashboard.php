<?php
require_once 'admin_header_logic.php';
$page_title = 'Dashboard';

// Fetch visitor stats
$today = date('Y-m-d');
$visitors_today_result = $conn->query("SELECT COUNT(DISTINCT ip_address) as count FROM visitors WHERE visit_date = '{$today}'");
$visitors_today = $visitors_today_result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin_themes/<?php echo $admin_theme; ?>">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <p>Welcome to the admin panel, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>! Please select a section from the sidebar to get started.</p>
                <p>Your role is: <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong></p>

                <div class="card" style="margin-top: 2rem;">
                    <h3>Site Statistics</h3>
                    <p><strong>Unique Visitors Today:</strong> <?php echo $visitors_today; ?></p>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

<?php
// This session start is redundant if admin_check is called first, but it's safe to have.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/css/admin_style.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="/admin/" class="logo">Admin Panel</a>
            </div>
            <nav class="admin-nav">
                <?php
                // Dynamically build the admin menu
                require_once __DIR__ . '/../../src/models/menu.php';
                $admin_menu_conn = db_connect();
                $admin_menu_items = get_menu_for_user($admin_menu_conn, 'admin', $_SESSION['user_id']);
                $admin_menu_conn->close();

                function render_menu(array $menu_items) {
                    echo '<ul>';
                    foreach ($menu_items as $item) {
                        echo '<li>';
                        echo '<a href="' . htmlspecialchars($item['link']) . '">' . htmlspecialchars($item['name']) . '</a>';
                        if (!empty($item['children'])) {
                            render_menu($item['children']); // Recursive call
                        }
                        echo '</li>';
                    }
                    echo '</ul>';
                }

                render_menu($admin_menu_items);
                ?>
            </nav>
        </aside>
        <div class="main-panel">
            <header class="admin-top-bar">
                <div class="welcome-message">
                    Welcome, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong>
                </div>
            </header>
            <main class="admin-main-content">
                <div class="admin-page-content">
                    <!-- Page-specific content starts here -->

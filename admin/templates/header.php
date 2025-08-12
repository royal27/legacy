<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin Panel</title>
    <!-- We can reuse the main stylesheet and add admin-specific overrides -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin-style.css">

    <!-- Toastr CSS for notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="logo-text">Admin</a>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                        <a href="index.php?page=dashboard">Dashboard</a>
                    </li>
                    <!-- More links will be added here based on features -->
                    <li class="<?php echo ($page === 'settings') ? 'active' : ''; ?>">
                        <a href="index.php?page=settings">Site Settings</a>
                    </li>
                    <li class="<?php echo ($page === 'users' || $page === 'edit_user') ? 'active' : ''; ?>">
                        <a href="index.php?page=users">Users</a>
                    </li>
                    <li class="<?php echo ($page === 'roles' || $page === 'permissions') ? 'active' : ''; ?>">
                        <a href="index.php?page=roles">Roles & Permissions</a>
                    </li>
                     <li class="<?php echo ($page === 'languages' || $page === 'translations') ? 'active' : ''; ?>">
                        <a href="index.php?page=languages">Languages</a>
                    </li>
                    <li class="<?php echo ($page === 'plugins') ? 'active' : ''; ?>">
                        <a href="index.php?page=plugins">Plugins</a>
                    </li>
                    <li class="<?php echo ($page === 'points') ? 'active' : ''; ?>">
                        <a href="index.php?page=points">Points System</a>
                    </li>
                    <li class="<?php echo ($page === 'security') ? 'active' : ''; ?>">
                        <a href="index.php?page=security">Security</a>
                    </li>

                    <li class="nav-heading">Appearance</li>
                    <li class="<?php echo ($page === 'menus') ? 'active' : ''; ?>">
                        <a href="index.php?page=menus">Menus</a>
                    </li>
                    <li class="<?php echo ($page === 'pages' || $page === 'edit_page') ? 'active' : ''; ?>">
                        <a href="index.php?page=pages">Pages</a>
                    </li>
                     <li class="<?php echo ($page === 'themes') ? 'active' : ''; ?>">
                        <a href="index.php?page=themes">Themes</a>
                    </li>

                    <li>
                        <a href="../index.php" target="_blank">View Site</a>
                    </li>
                </ul>
            </nav>
        </aside>
        <div class="admin-main-content">
            <header class="admin-top-header">
                <div class="welcome-message">
                    Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!
                </div>
                <div class="header-actions">
                    <a href="logout.php" class="btn btn-accent">Logout</a>
                </div>
            </header>
            <main class="admin-page-content">
                <h1><?php echo htmlspecialchars($page_title); ?></h1>
                <hr>

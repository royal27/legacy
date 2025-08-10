<?php
// This is the header for the admin panel.
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['current_language']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('admin_area_title', 'Admin Area'); ?></title>
    <!-- We use the main site's stylesheet for consistency -->
    <link rel="stylesheet" href="../templates/default/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* Admin-specific styles */
        :root { --admin-bg: #f0f2f5; --admin-sidebar-bg: #ffffff; --admin-text: #333; --admin-hover: #e4e6eb; }
        body.dark-mode { --admin-bg: #1c1e21; --admin-sidebar-bg: #242526; --admin-text: #e4e6eb; --admin-hover: #3a3b3c; }

        body { background: var(--admin-bg) !important; color: var(--admin-text) !important; }
        .admin-wrapper { display: flex; }
        .admin-sidebar { width: 250px; background: var(--admin-sidebar-bg); min-height: 100vh; padding: 20px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .admin-content { flex: 1; padding: 20px; }
        .admin-sidebar h2 { margin-top: 0; }
        .admin-sidebar ul { list-style: none; padding: 0; }
        .admin-sidebar ul li a { display: block; padding: 10px; text-decoration: none; color: var(--admin-text); border-radius: 5px; margin-bottom: 5px; }
        .admin-sidebar ul li a:hover { background: var(--admin-hover); }
        main.admin-content { background: var(--admin-bg) !important; } /* Override template */
    </style>
</head>
<body class="light-mode"> <!-- Add a class for dark-mode toggle if needed -->

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <h2><?php echo t('admin_menu_title', 'Admin Menu'); ?></h2>
        <ul>
            <li><a href="index.php"><?php echo t('admin_menu_dashboard', 'Dashboard'); ?></a></li>
            <li><a href="manage_users.php"><?php echo t('admin_menu_users', 'Manage Users'); ?></a></li>
            <li><a href="manage_languages.php"><?php echo t('admin_menu_languages', 'Manage Languages'); ?></a></li>
            <li><a href="manage_plugins.php"><?php echo t('admin_menu_plugins', 'Manage Plugins'); ?></a></li>
            <li><a href="manage_links.php"><?php echo t('admin_menu_links', 'Manage Links'); ?></a></li>
            <li><a href="settings.php"><?php echo t('admin_menu_settings', 'Site Settings'); ?></a></li>
            <li><hr></li>
            <li><a href="../" target="_blank"><?php echo t('admin_menu_view_site', 'View Site'); ?></a></li>
            <li><a href="logout.php"><?php echo t('admin_menu_logout', 'Logout'); ?></a></li>
        </ul>
    </aside>
    <main class="admin-content">
        <!-- Main content of the admin page will start here -->

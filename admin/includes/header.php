<?php
// This is the header for the admin panel.
// It assumes that the session has been started and core files are included.
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['current_language']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('admin_area_title', 'Admin Area'); ?></title>
    <!-- We can use the main site's stylesheet to keep things consistent -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/templates/default/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* Some specific styles for the admin area */
        body { background: #f0f2f5; }
        body.dark-mode { background: #1c1e21; }
        .admin-wrapper { display: flex; }
        .admin-sidebar { width: 250px; background: #fff; min-height: 100vh; padding: 20px; }
        body.dark-mode .admin-sidebar { background: #242526; border-right: 1px solid #3a3b3c; }
        .admin-content { flex: 1; padding: 20px; }
        .admin-sidebar h2 { margin-top: 0; }
        .admin-sidebar ul { list-style: none; padding: 0; }
        .admin-sidebar ul li a { display: block; padding: 10px; text-decoration: none; color: #333; border-radius: 5px; }
        body.dark-mode .admin-sidebar ul li a { color: #e4e6eb; }
        .admin-sidebar ul li a:hover { background: #e4e6eb; }
        body.dark-mode .admin-sidebar ul li a:hover { background: #3a3b3c; }
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

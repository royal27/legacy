<!DOCTYPE html>
<html lang="<?php echo $_SESSION['current_language']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('site_title', 'My Awesome Website'); ?></title>
    <!-- Toastr CSS for notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Main stylesheet for the template -->
    <link rel="stylesheet" href="<?php echo $active_template_path; ?>/assets/css/style.css">
</head>
<body>

<header>
    <nav>
        <a href="/"><?php echo t('nav_home', 'Home'); ?></a>
        <a href="/dashboard"><?php echo t('nav_dashboard', 'Dashboard'); ?></a>
        <a href="/admin"><?php echo t('nav_admin', 'Admin'); ?></a>
    </nav>
    <?php do_action('after_header_nav'); ?>
</header>

<main>
    <!-- Page content will be loaded here -->

<!DOCTYPE html>
<html lang="<?php echo Language::getCurrentLanguage(); ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(Hooks::apply_filters('page_title', isset($data['title']) ? $data['title'] : 'My Website')); ?></title>
    <link rel="stylesheet" href="/public/css/toastr.min.css">
    <link rel="stylesheet" href="/public/css/vibrant.css">
    <?php Hooks::do_action('head_content'); ?>
</head>
<body>

<header class="main-header">
    <div class="container">
        <nav class="main-nav">
            <a href="/" class="gradient-text"><b>MySite</b></a>

            <div class="nav-links">
                <?php
                // Main navigation
                foreach (App::$menu_links as $link) {
                    // Simple visibility logic for default links
                    if ($link['url'] == '/dashboard' && !Auth::isLoggedIn()) continue;
                    if (($link['url'] == '/users/login' || $link['url'] == '/users/register') && Auth::isLoggedIn()) continue;

                    echo '<a href="' . htmlspecialchars($link['url']) . '">' . htmlspecialchars($link['title']) . '</a>';
                }

                // Add Admin Panel and Logout to the main nav for mobile view
                if (Auth::isLoggedIn()) {
                    if (Auth::check('admin.access')) {
                        echo '<a href="/admin">Admin Panel</a>';
                    }
                    echo '<a href="/users/logout">Logout</a>';
                }
                ?>
            </div>

            <button class="nav-toggle" id="nav-toggle">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </button>
        </nav>
    </div>
</header>

<main class="container">
    <!-- Main content will be loaded here -->

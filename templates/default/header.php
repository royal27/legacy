<!DOCTYPE html>
<html lang="<?php echo Language::getCurrentLanguage(); ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(Hooks::apply_filters('page_title', isset($data['title']) ? $data['title'] : 'My Website')); ?></title>
    <link rel="stylesheet" href="/public/css/toastr.min.css">
    <link rel="stylesheet" href="/public/css/style.css">
    <?php Hooks::do_action('head_content'); ?>
</head>
<body>

<header class="main-header">
    <div class="container">
        <nav style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <a href="/" class="gradient-text"><b>MySite</b></a>
                <?php
                // Main navigation
                foreach (App::$menu_links as $link) {
                    // Simple visibility logic for default links
                    if ($link['url'] == '/dashboard' && !Auth::isLoggedIn()) continue;
                    if (($link['url'] == '/users/login' || $link['url'] == '/users/register') && Auth::isLoggedIn()) continue;

                    echo '<a href="' . htmlspecialchars($link['url']) . '">' . htmlspecialchars($link['title']) . '</a>';
                }
                ?>
            </div>
            <div>
                <?php if (Auth::isLoggedIn()): ?>
                    <?php if (Auth::check('admin.access')): ?>
                        <a href="/admin">Admin Panel</a>
                    <?php endif; ?>
                    <a href="/users/logout">Logout</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<main class="container">
    <!-- Main content will be loaded here -->

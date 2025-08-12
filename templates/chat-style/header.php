<!DOCTYPE html>
<html lang="<?php echo Language::getCurrentLanguage(); ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(Hooks::apply_filters('page_title', isset($data['title']) ? $data['title'] : 'My Website')); ?></title>
    <link rel="stylesheet" href="/public/css/toastr.min.css">
    <link rel="stylesheet" href="/public/css/chat-style.css">
    <?php Hooks::do_action('head_content'); ?>
</head>
<body>

<div class="sidebar">
    <a href="/" class="gradient-text" style="display: block; margin-bottom: 2rem; font-size: 1.5rem;"><b>MySite</b></a>
    <nav class="sidebar-nav">
        <?php
        // Main navigation
        foreach (App::$menu_links as $link) {
            // Simple visibility logic for default links
            if ($link['url'] == '/dashboard' && !Auth::isLoggedIn()) continue;
            if (($link['url'] == '/users/login' || $link['url'] == '/users/register') && Auth::isLoggedIn()) continue;

            echo '<a href="' . htmlspecialchars($link['url']) . '">' . htmlspecialchars($link['title']) . '</a>';
        }
        ?>
    </nav>
</div>

<div class="main-content-wrapper">
    <header class="chat-header">
        <div class="header-content">
            <!-- Can add breadcrumbs or page title here in the future -->
            <div></div>
            <div class="profile-area">
                <button id="profile-menu-trigger" class="profile-trigger">
                    <span><?php echo Auth::isLoggedIn() ? htmlspecialchars(Auth::user()['username']) : 'Guest'; ?></span>
                    <span class="arrow-down">▼</span>
                </button>
                <div id="profile-dropdown" class="profile-dropdown">
                    <?php if (Auth::isLoggedIn()): ?>
                        <a href="/dashboard">Dashboard</a>
                        <?php if (Auth::check('admin.access')): ?>
                            <a href="/admin">Admin Panel</a>
                        <?php endif; ?>
                        <a href="#">Settings</a>
                        <hr>
                        <a href="/users/logout">Logout</a>
                    <?php else: ?>
                        <a href="/users/login">Login</a>
                        <a href="/users/register">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <!-- Main content will be loaded here -->

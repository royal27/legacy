<?php
// Prevent direct file access
if (!defined('APP_LOADED')) {
    http_response_code(403);
    die('Forbidden');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'My Website'; ?></title>

    <!-- Google Fonts: Dancing Script for handwritten style -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo $settings['theme_url']; ?>/css/style.css">

    <!-- jQuery (required for plugins and other scripts) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="page-wrapper">
        <header class="main-header">
            <div class="logo">
                <a href="<?php echo SITE_URL; ?>">
                    <?php if ($settings['logo_type'] === 'image' && !empty($settings['logo_image'])): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($settings['logo_image']); ?>" alt="<?php echo htmlspecialchars($settings['site_title']); ?> Logo" class="logo-image">
                    <?php else: ?>
                        <span class="logo-text"><?php echo htmlspecialchars($settings['logo_text']); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <?php
                    $main_menu_items = get_menu('main_nav');
                    foreach ($main_menu_items as $item):
                        // Check if the URL is external or internal
                        $url = (filter_var($item['url'], FILTER_VALIDATE_URL)) ? $item['url'] : SITE_URL . '/' . ltrim($item['url'], '/');
                    ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($url); ?>" target="<?php echo htmlspecialchars($item['target']); ?>">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <div class="user-nav">
                <ul>
                    <?php include __DIR__ . '/partials/language_switcher.php'; ?>
                    <?php if (is_logged_in()): ?>
                        <li><a href="<?php echo rtrim(SITE_URL, '/'); ?>/profile/<?php echo $_SESSION['user_id']; ?>">My Profile</a></li>
                        <li><a href="<?php echo rtrim(SITE_URL, '/'); ?>/edit-profile">Edit Profile</a></li>
                        <li>
                            <form action="<?php echo rtrim(SITE_URL, '/'); ?>/logout" method="post" style="display:inline; margin:0; padding:0;">
                                <input type="hidden" name="_token" value="<?php echo generate_csrf_token(); ?>">
                                <button type="submit" class="btn-link-style">Logout</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="<?php echo SITE_URL; ?>/login">Login</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/register" class="btn btn-primary btn-sm">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </header>

        <main class="main-content">
            <div class="container">

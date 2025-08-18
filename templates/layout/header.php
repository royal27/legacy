<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Load settings to be used in the template
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/models/setting.php';
$db_conn_for_settings = db_connect();
$site_settings = get_all_settings($db_conn_for_settings);
$db_conn_for_settings->close();

// Load active plugins
require_once __DIR__ . '/../../src/includes/plugin_handler.php';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_settings['site_name'] ?? 'My Awesome Site'); ?></title>
    <?php
        $active_theme = $site_settings['active_theme'] ?? 'default';
        $theme_stylesheet = "/themes/" . htmlspecialchars($active_theme) . "/style.css";
    ?>
    <link rel="stylesheet" href="<?php echo $theme_stylesheet; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php
        // Dynamically load the font from settings
        $font_family = $site_settings['default_font'] ?? 'Dancing Script';
        $font_query = urlencode($font_family);
    ?>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo $font_query; ?>:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body {
            font-family: '<?php echo $font_family; ?>', cursive;
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="site-wrapper">
        <header class="main-header">
            <div class="container">
                <div class="logo">
                    <a href="/">
                        <?php if (!empty($site_settings['site_logo_path'])): ?>
                            <img src="/uploads/<?php echo htmlspecialchars($site_settings['site_logo_path']); ?>" alt="<?php echo htmlspecialchars($site_settings['site_name']); ?> Logo">
                        <?php else: ?>
                            <?php echo htmlspecialchars($site_settings['site_name'] ?? 'My Awesome Site'); ?>
                        <?php endif; ?>
                    </a>
                </div>
                <nav class="main-nav">
                    <ul>
                        <?php
                        // Dynamically build the public menu
                        $public_menu_conn = db_connect();
                        // Pass user_id if logged in, otherwise null
                        $user_id_for_menu = $_SESSION['user_id'] ?? null;
                        $public_menu_items = get_menu_for_user($public_menu_conn, 'public', $user_id_for_menu);
                        $public_menu_conn->close();

                        foreach ($public_menu_items as $item) {
                            // This simple renderer doesn't support sub-menus for the public site yet.
                            echo '<li><a href="' . htmlspecialchars($item['link']) . '">' . htmlspecialchars($item['name']) . '</a></li>';
                        }

                        // Add login/logout/register links manually as they are contextual
                        if (isset($_SESSION['user_id'])) {
                            echo '<li><a href="/dashboard.php">Dashboard</a></li>';
                            echo '<li><a href="/logout.php">Logout</a></li>';
                        } else {
                            echo '<li><a href="/login.php">Login</a></li>';
                            echo '<li><a href="/register.php">Register</a></li>';
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </header>
        <main class="main-content">
            <div class="container">

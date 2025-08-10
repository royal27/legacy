<?php
// Enable full error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DS', DIRECTORY_SEPARATOR);
session_start();

// Load essential files.
require_once __DIR__ . DS . '..' . DS . 'includes' . DS . 'config.php';
require_once __DIR__ . DS . '..' . DS . 'includes' . DS . 'database.php';
require_once __DIR__ . DS . '..' . DS . 'includes' . DS . 'functions.php';

$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'founder';

require_once __DIR__ . DS . '..' . DS . 'includes' . DS . 'language.php';

if ($is_logged_in) {
    require_once __DIR__ . DS . 'includes' . DS . 'header.php';
    ?>
    <h1><?php echo t('admin_dashboard_title', 'Admin Dashboard'); ?></h1>
    <p><?php echo sprintf(t('admin_welcome_message', 'Welcome, %s!'), t($_SESSION['username'])); ?></p>
    <h3><?php echo t('dashboard_quick_stats', 'Quick Stats'); ?></h3>
    <div>
        <?php
            $users_count = $mysqli->query("SELECT COUNT(*) as count FROM `".DB_PREFIX."users`")->fetch_assoc()['count'];
            $langs_count = $mysqli->query("SELECT COUNT(*) as count FROM `".DB_PREFIX."languages`")->fetch_assoc()['count'];
            $plugins_count = $mysqli->query("SELECT COUNT(*) as count FROM `".DB_PREFIX."plugins` WHERE is_active = 1")->fetch_assoc()['count'];
        ?>
        <p><?php echo sprintf(t('dashboard_stat_users', 'Total Users: %d'), $users_count); ?></p>
        <p><?php echo sprintf(t('dashboard_stat_languages', 'Total Languages: %d'), $langs_count); ?></p>
        <p><?php echo sprintf(t('dashboard_stat_plugins', 'Active Plugins: %d'), $plugins_count); ?></p>
    </div>
    <?php
    require_once __DIR__ . DS . 'includes' . DS . 'footer.php';
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8"><title><?php echo t('admin_login_title', 'Admin Login'); ?></title>
        <link rel="stylesheet" href="../templates/default/assets/css/style.css">
        <style>
             body { display: flex; justify-content: center; align-items: center; min-height: 100vh; }
            .login-container { max-width: 400px; width: 100%; padding: 40px; background: rgba(255,255,255,0.9); border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); color: #333; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2><?php echo t('admin_login_title', 'Admin Login'); ?></h2>
            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<p style="color: red;">' . t($_SESSION['login_error']) . '</p>';
                unset($_SESSION['login_error']);
            }
            ?>
            <form action="login.php" method="post">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="username"><?php echo t('login_label_username', 'Username'); ?></label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="password"><?php echo t('login_label_password', 'Password'); ?></label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit"><?php echo t('login_button', 'Login'); ?></button>
            </form>
        </div>
    </body>
    </html>
    <?php
}
?>

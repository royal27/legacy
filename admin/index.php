<?php
// Enable full error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Admin Panel Entry Point
session_start();

// Load essential files.
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Check if the user is logged in and has an admin-level role.
$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'founder';

// The language system is loaded after a potential login action,
// so the user's preferred language is immediately available.
require_once __DIR__ . '/../includes/language.php';


if ($is_logged_in) {
    // --- Logged-in Admin View ---

    // Load the admin-specific header
    require_once __DIR__ . '/includes/header.php';

    // --- Dashboard Content ---
    ?>
    <h1><?php echo t('admin_dashboard_title', 'Admin Dashboard'); ?></h1>
    <p><?php echo sprintf(t('admin_welcome_message', 'Welcome, %s!'), htmlspecialchars($_SESSION['username'])); ?></p>

    <h3><?php echo t('dashboard_quick_stats', 'Quick Stats'); ?></h3>
    <div class="quick-stats">
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
    // Load the admin-specific footer
    require_once __DIR__ . '/includes/footer.php';

} else {
    // --- Login Form View ---
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo t('admin_login_title', 'Admin Login'); ?></title>
        <link rel="stylesheet" href="../templates/default/assets/css/style.css">
        <style>
             body {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
             }
            .login-container {
                max-width: 400px;
                width: 100%;
                padding: 40px;
                background: rgba(255,255,255,0.9);
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                color: #333;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2><?php echo t('admin_login_title', 'Admin Login'); ?></h2>

            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<p style="color: red;">' . htmlspecialchars($_SESSION['login_error']) . '</p>';
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

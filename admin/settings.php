<?php
define('DS', DIRECTORY_SEPARATOR);
session_start();

require_once __DIR__ . DS . '..' . DS . 'includes' . DS . 'config.php';
require_once __DIR__ . DS . '..' . DS . 'includes' . DS . 'database.php';
require_once __DIR__ . DS . '..' . DS . 'includes' . DS . 'functions.php';
require_once __DIR__ . DS . '..' . DS . 'includes' . DS . 'language.php';

$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'founder';
if (!$is_logged_in) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . DS . 'includes' . DS . 'header.php';
?>

<h1><?php echo t('site_settings_title', 'Site Settings'); ?></h1>
<p><?php echo t('site_settings_description', 'This page will allow you to manage global settings for your site, such as the site name and default language.'); ?></p>
<p><em>(<?php echo t('feature_under_development', 'This feature is under development.'); ?>)</em></p>

<?php
require_once __DIR__ . DS . 'includes' . DS . 'footer.php';
?>

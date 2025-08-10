<?php
// Admin Page: Manage Links
session_start();

// --- Load core files and check user permissions ---
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/language.php';

$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'founder';
if (!$is_logged_in) {
    header('Location: index.php');
    exit;
}

// --- Load the admin template ---
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page content starts here -->
<h1><?php echo t('manage_links_title', 'Manage Links'); ?></h1>
<p><?php echo t('manage_links_description', 'This page will allow you to manage navigation links and other links on your site.'); ?></p>
<p><em>(<?php echo t('feature_under_development', 'This feature is under development.'); ?>)</em></p>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

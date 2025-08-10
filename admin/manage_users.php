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

$prefix = DB_PREFIX;
$result = $mysqli->query("SELECT id, username, email, role, created_at FROM `{$prefix}users` ORDER BY id ASC");
$users = $result->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . DS . 'includes' . DS . 'header.php';
?>

<h1><?php echo t('manage_users_title', 'Manage Users'); ?></h1>
<p><?php echo t('manage_users_description', 'Here you can view and manage the users of your site.'); ?></p>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background: #eee; color: #333;">
            <th style="padding: 10px; text-align: left;">ID</th>
            <th style="padding: 10px; text-align: left;"><?php echo t('table_header_username', 'Username'); ?></th>
            <th style="padding: 10px; text-align: left;"><?php echo t('table_header_email', 'Email'); ?></th>
            <th style="padding: 10px; text-align: left;"><?php echo t('table_header_role', 'Role'); ?></th>
            <th style="padding: 10px; text-align: left;"><?php echo t('table_header_registered', 'Registered'); ?></th>
            <th style="padding: 10px; text-align: left;"><?php echo t('table_header_actions', 'Actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo t($user['id']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo t($user['username']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo t($user['email']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo t($user['role']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo t($user['created_at']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                <a href="#"><?php echo t('action_edit', 'Edit'); ?></a> |
                <a href="#" style="color: red;"><?php echo t('action_delete', 'Delete'); ?></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require_once __DIR__ . DS . 'includes' . DS . 'footer.php';
?>

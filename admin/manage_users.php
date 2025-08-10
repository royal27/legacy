<?php
// Admin Page: Manage Users
session_start();

// --- Load core files and check user permissions ---
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/language.php';

$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'founder';

if (!$is_logged_in) {
    // If user is not logged in, redirect to login page
    header('Location: index.php');
    exit;
}

// --- Page logic ---
// Fetch all users from the database
$prefix = DB_PREFIX;
$result = $mysqli->query("SELECT id, username, email, role, created_at FROM `{$prefix}users` ORDER BY id ASC");
$users = $result->fetch_all(MYSQLI_ASSOC);


// --- Load the admin template ---
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page content starts here -->
<h1><?php echo t('manage_users_title', 'Manage Users'); ?></h1>
<p><?php echo t('manage_users_description', 'Here you can view and manage the users of your site.'); ?></p>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background: #eee;">
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
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($user['id']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($user['username']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($user['email']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($user['role']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($user['created_at']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                <!-- Action buttons will go here (e.g., Edit, Delete) -->
                <a href="#"><?php echo t('action_edit', 'Edit'); ?></a> |
                <a href="#" style="color: red;"><?php echo t('action_delete', 'Delete'); ?></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

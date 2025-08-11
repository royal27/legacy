<?php
// This is a public-facing page
define('APP_LOADED', true);

// Bootstrap the application to get DB connection, functions, etc.
require_once 'core/bootstrap.php';

// Get user ID from URL
$user_id = (int)($_GET['id'] ?? 0);
if ($user_id === 0) {
    // Redirect to homepage if no user ID is provided
    redirect('index.php');
}

// Fetch user data
$stmt = $db->prepare("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// If user not found, redirect
if (!$user) {
    redirect('index.php');
}

$page_title = "Profile: " . htmlspecialchars($user['username']);
include 'templates/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <img src="uploads/avatars/<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'default.png'; ?>" alt="<?php echo htmlspecialchars($user['username']); ?>'s Avatar">
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['username']); ?></h1>
            <span class="role-badge"><?php echo htmlspecialchars($user['role_name']); ?></span>
            <p>Member since: <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
            <div class="points-badge">
                <strong><?php echo $user['points']; ?></strong> Points
            </div>
        </div>
    </div>

    <div class="profile-content">
        <!-- More profile details or user activity can go here -->
        <p>This is the public profile page for <?php echo htmlspecialchars($user['username']); ?>.</p>
    </div>

    <?php // --- Admin Moderation Panel ---
    if (is_admin() && user_has_permission('manage_users')): ?>
    <div class="admin-moderation-panel">
        <h3>Admin Actions</h3>
        <p>
            <a href="admin/index.php?page=edit_user&id=<?php echo $user['id']; ?>" class="btn btn-primary">Edit User in Admin Panel</a>
            <!-- Quick actions can be added here later via AJAX -->
        </p>
    </div>
    <?php endif; ?>
</div>

<style>
.profile-container { max-width: 900px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 5px; }
.profile-header { display: flex; gap: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
.profile-avatar img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-primary); }
.profile-info h1 { margin: 0; }
.role-badge { background: var(--color-secondary); color: white; padding: 3px 8px; border-radius: 5px; font-size: 0.9em; }
.points-badge { background: #ffc107; color: #333; padding: 5px 10px; border-radius: 5px; display: inline-block; margin-top: 10px; }
.admin-moderation-panel { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; margin-top: 20px; border-radius: 5px; }
</style>

<?php
include 'templates/footer.php';
?>

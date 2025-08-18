<?php
require_once __DIR__ . '/../src/includes/auth_check.php';
require_once __DIR__ . '/../src/includes/csrf.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/models/user.php';

$conn = db_connect();
$user_id = $_SESSION['user_id'];

// --- FORM PROCESSING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_token();
    // --- Update Profile Details (Username, Email, etc.) ---
    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $bio = trim($_POST['bio']);

        // Fetch current user data to check for changes
        $user_stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $current_user = $user_stmt->get_result()->fetch_assoc();
        $user_stmt->close();

        // Check if username or email is being changed and if the new one is already taken
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? AND username != ?) OR (email = ? AND email != ?)");
        $check_stmt->bind_param("ssss", $username, $current_user['username'], $email, $current_user['email']);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            $_SESSION['errors'] = ['Username or email is already taken by another account.'];
        } else {
            $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, bio = ? WHERE id = ?");
            $update_stmt->bind_param("ssssi", $username, $email, $full_name, $bio, $user_id);
            if ($update_stmt->execute()) {
                $_SESSION['username'] = $username; // Update session username
                $_SESSION['success_message'] = 'Profile updated successfully.';
            } else {
                $_SESSION['errors'] = ['Failed to update profile.'];
            }
            $update_stmt->close();
        }
        $check_stmt->close();
    }

    // --- Change Password ---
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $pass_stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $pass_stmt->bind_param("i", $user_id);
        $pass_stmt->execute();
        $user_data = $pass_stmt->get_result()->fetch_assoc();

        if (password_verify($current_password, $user_data['password'])) {
            if (strlen($new_password) >= 8 && $new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_pass_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_pass_stmt->bind_param("si", $hashed_password, $user_id);
                $update_pass_stmt->execute();
                $_SESSION['success_message'] = 'Password changed successfully.';
            } else {
                $_SESSION['errors'] = ['New password must be at least 8 characters long and match the confirmation.'];
            }
        } else {
            $_SESSION['errors'] = ['Incorrect current password.'];
        }
    }

    header("Location: user_settings.php");
    exit();
}

// Fetch user data for displaying in the form
$stmt = $conn->prepare("SELECT username, email, full_name, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$conn->close();

// Generate a token for the forms
csrf_generate_token();
?>

<?php require_once __DIR__ . '/../templates/layout/header.php'; ?>

<h2>User Settings</h2>
<p>Manage your account details and password.</p>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['errors'])) {
    echo '<div class="alert alert-danger" style="color:white;">' . htmlspecialchars(implode(', ', $_SESSION['errors'])) . '</div>';
    unset($_SESSION['errors']);
}
?>

<div class="form-container" style="background:rgba(0,0,0,0.2);">
    <h3>Profile Information</h3>
    <form action="user_settings.php" method="POST">
        <?php echo csrf_input(); ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea name="bio" id="bio" class="form-control" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
        </div>
        <button type="submit" name="update_profile" class="btn">Update Profile</button>
    </form>
</div>

<div class="form-container" style="margin-top:2rem; background:rgba(0,0,0,0.2);">
    <h3>Change Password</h3>
    <form action="user_settings.php" method="POST">
        <?php echo csrf_input(); ?>
        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>
        <button type="submit" name="change_password" class="btn">Change Password</button>
    </form>
</div>

<?php require_once __DIR__ . '/../templates/layout/footer.php'; ?>

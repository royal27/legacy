<?php
require_once 'admin_header_logic.php';
$page_title = 'Manage Users';

$message = '';
// Handle invitation form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_user'])) {
    $email = $_POST['email'];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

            $stmt = $conn->prepare("INSERT INTO user_invitations (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $token, $expires_at);

            if ($stmt->execute()) {
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'];
                $base_dir = dirname(dirname($_SERVER['PHP_SELF']));
                $registration_link = "{$protocol}://{$host}{$base_dir}/register.php?token={$token}";

                $subject = "You are invited to join our platform!";
                $body = "Please click the following link to register: " . $registration_link;
                $headers = "From: no-reply@" . $host;

                if (mail($email, $subject, $body, $headers)) {
                    $message = "Invitation sent successfully to {$email}.";
                } else {
                    $message = "Invitation created, but failed to send email. Please check server configuration. Link: " . $registration_link;
                }
            }
        } catch (Exception $e) {
            $message = "An error occurred: " . $e->getMessage();
        }
    } else {
        $message = "Invalid email address.";
    }
}

// Fetch all users
$users = $conn->query("SELECT id, username, role, first_name, last_name FROM users ORDER BY username");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/admin_themes/<?php echo $admin_theme; ?>">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <?php if ($message): ?>
                    <p class="message success"><?php echo $message; ?></p>
                <?php endif; ?>
                <?php
                // Fetch invitation setting
                $invitation_setting_result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'enable_invitations'");
                $invitations_enabled = $invitation_setting_result->fetch_assoc()['setting_value'] ?? '0';
                ?>

                <a href="user-add.php" class="btn btn-add"><?php echo $lang_ui['add_new']; ?> User</a>

                <?php if ($invitations_enabled == 1): ?>
                <div class="card">
                    <h3><?php echo $lang_ui['invite_new_user']; ?></h3>
                    <form action="users.php" method="post" class="invite-form">
                        <div class="input-group">
                            <label for="email"><?php echo $lang_ui['email_address']; ?></label>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <button type="submit" name="invite_user"><?php echo $lang_ui['send_invitation']; ?></button>
                    </form>
                </div>
                <?php endif; ?>

                <table>
                    <thead>
                        <tr>
                            <th><?php echo $lang_ui['username']; ?></th>
                            <th><?php echo $lang_ui['full_name']; ?></th>
                            <th><?php echo $lang_ui['role']; ?></th>
                            <th><?php echo $lang_ui['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td>
                                    <a href="user-edit.php?id=<?php echo $user['id']; ?>" class="btn btn-primary"><?php echo $lang_ui['edit']; ?></a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): // Prevent self-deletion ?>
                                        <a href="user-delete.php?id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');"><?php echo $lang_ui['delete']; ?></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

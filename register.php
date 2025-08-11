<?php
require_once 'includes/connect.php';
require_once 'includes/functions.php';

// Check if invitation system is enabled
$invitation_setting_result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'enable_invitations'");
$invitations_enabled = $invitation_setting_result->fetch_assoc()['setting_value'] ?? '0';

if ($invitations_enabled != 1) {
    die("Registration via invitation is currently disabled.");
}

// Validate token
if (!isset($_GET['token'])) {
    die("Invalid invitation link. No token provided.");
}

$token = $_GET['token'];
$stmt = $conn->prepare("SELECT * FROM user_invitations WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$invitation = $stmt->get_result()->fetch_assoc();

if (!$invitation) {
    die("Invalid or expired invitation token.");
}

$email = $invitation['email'];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Re-verify token on POST
    $token_post = $_POST['token'];
    $stmt_post = $conn->prepare("SELECT * FROM user_invitations WHERE token = ? AND expires_at > NOW()");
    $stmt_post->bind_param("s", $token_post);
    $stmt_post->execute();
    if (!$stmt_post->get_result()->fetch_assoc()) {
        die("Invalid or expired token on submission.");
    }

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $role = 'waiter'; // Invited users are always waiters

    $conn->begin_transaction();
    try {
        $stmt_insert = $conn->prepare("INSERT INTO users (username, password, email, role, first_name, last_name, gender, age) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("sssssssi", $username, $password, $email, $role, $first_name, $last_name, $gender, $age);
        $stmt_insert->execute();

        $stmt_delete = $conn->prepare("DELETE FROM user_invitations WHERE token = ?");
        $stmt_delete->bind_param("s", $token_post);
        $stmt_delete->execute();

        $conn->commit();
        $message = 'Registration successful! You can now log in to the admin panel.';
    } catch (Exception $e) {
        $conn->rollback();
        $message = "An error occurred: " . $e->getMessage();
    }
}

// Fetch settings for template
$settings_result = $conn->query("SELECT * FROM settings");
$settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container" style="width: 400px;">
        <h2>Complete Your Registration</h2>
        <?php if ($message): ?>
            <p class="message success"><?php echo $message; ?></p>
            <a href="/admin">Go to Login</a>
        <?php else: ?>
            <form action="register.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" disabled>
                </div>
                 <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <hr>
                <h3>Personal Details</h3>
                 <div class="input-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name">
                </div>
                <div class="input-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name">
                </div>
                <div class="input-group">
                    <label for="gender">Gender</label>
                    <select name="gender" id="gender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age">
                </div>
                <button type="submit">Register</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

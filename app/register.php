<?php
// Prevent direct file access
if (!defined('APP_LOADED')) {
    http_response_code(403);
    die('Forbidden');
}

// If user is already logged in, redirect to their profile
if (is_logged_in()) {
    redirect(rtrim(SITE_URL, '/') . '/profile/' . $_SESSION['user_id']);
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token();
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // --- Validation ---
    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Check if username or email already exist
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = 'Username or email is already taken.';
    }
    $stmt->close();

    // --- If no errors, create user ---
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $initial_points = (int)($settings['points_on_register'] ?? 50);
        $default_role_id = (int)($settings['default_role_id'] ?? 2);


        $stmt = $db->prepare("INSERT INTO users (username, email, password, points, role_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssid', $username, $email, $hashed_password, $initial_points, $default_role_id);

        if ($stmt->execute()) {
            // Registration success, log the user in
            $new_user_id = $stmt->insert_id;
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role_id'] = $default_role_id;
            load_user_permissions();

            // Redirect to login page with a success message
            redirect(rtrim(SITE_URL, '/') . '/login?registered=1');
        } else {
            $errors[] = 'An error occurred during registration. Please try again.';
        }
        $stmt->close();
    }
}

$page_title = "Register";
?>
<div class="container">
    <div class="login-form-container">
        <h1>Create an Account</h1>
        <?php if (!empty($errors)): ?>
            <div class="message-box error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="<?php echo rtrim(SITE_URL, '/'); ?>/register" method="post">
            <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
             <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Register</button>
        </form>
        <p class="text-center">Already have an account? <a href="<?php echo rtrim(SITE_URL, '/'); ?>/login">Login here</a>.</p>
    </div>
</div>

<style>
.login-form-container { max-width: 500px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
.text-center { text-align: center; margin-top: 20px; }
.message-box ul { padding-left: 20px; text-align: left; }
</style>

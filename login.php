<?php
define('APP_LOADED', true);
require_once 'core/bootstrap.php';

// If user is already logged in, redirect to their profile
if (is_logged_in()) {
    redirect('profile.php?id=' . $_SESSION['user_id']);
}

$error_message = '';
$success_message = '';

if (isset($_GET['kicked'])) {
    $error_message = 'You have been logged out by an administrator.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Username and password are required.';
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($user['is_banned']) {
                $error_message = 'This account has been banned.';
            } elseif (password_verify($password, $user['password'])) {
                // Login success! Check for daily points
                $today = date('Y-m-d');
                if ($user['last_login_points_awarded'] != $today) {
                    $points_for_login = (int)($settings['points_on_login'] ?? 10);
                    if ($points_for_login > 0) {
                        $db->query("UPDATE users SET points = points + {$points_for_login}, last_login_points_awarded = '{$today}' WHERE id = {$user['id']}");
                    }
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];
                load_user_permissions(); // Load permissions on login

                redirect('profile.php?id=' . $user['id']);
            } else {
                $error_message = 'Invalid username or password.';
            }
        } else {
            $error_message = 'Invalid username or password.';
        }
        $stmt->close();
    }
}

$page_title = "Login";
include 'templates/header.php';
?>
<div class="container">
    <div class="login-form-container">
        <h1>Login to your Account</h1>
        <?php if (!empty($error_message)): ?>
            <div class="message-box error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Login</button>
        </form>
        <p class="text-center">Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</div>

<style>
.login-form-container { max-width: 500px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
.text-center { text-align: center; margin-top: 20px; }
</style>

<?php
include 'templates/footer.php';
?>

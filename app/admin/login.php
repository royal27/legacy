<?php
// Prevent direct file access
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

// This file is included by admin/index.php when the user is not logged in.
// If user is already logged in, the router in index.php should prevent this from being loaded.
if (is_admin()) {
    redirect(rtrim(SITE_URL, '/') . '/admin/');
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token is validated in the main entry point (admin/index.php) now.
    // Let's re-add it here for standalone safety, but the main router should handle it.
    if (function_exists('validate_csrf_token')) {
        validate_csrf_token();
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Username and password are required.';
    } else {
        $stmt = $db->prepare("SELECT id, password, role_id FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['role_id'] = $user['role_id'];
                load_user_permissions();

                if (is_admin()) {
                    redirect(rtrim(SITE_URL, '/') . '/admin/');
                } else {
                     $error_message = 'You do not have permission to access the admin panel.';
                }
            } else {
                $error_message = 'Invalid username or password.';
            }
        } else {
            $error_message = 'Invalid username, password, or insufficient permissions.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="<?php echo rtrim(SITE_URL, '/'); ?>/app/assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .login-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Panel</h1>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="<?php echo rtrim(SITE_URL, '/'); ?>/admin/login.php" method="post">
            <input type="hidden" name="_token" value="<?php echo generate_csrf_token(); ?>">
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
    </div>
</body>
</html>

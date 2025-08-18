<?php
require_once __DIR__ . '/../src/includes/csrf.php';
require_once __DIR__ . '/../templates/layout/header.php';

// If user is already logged in, redirect them to the dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit();
}
csrf_generate_token();
?>

<div class="form-container">
    <h2>Login</h2>

    <?php
    // Display error messages if they exist in the session
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        echo '<div class="alert alert-danger">';
        foreach ($_SESSION['errors'] as $error) {
            echo htmlspecialchars($error) . '<br>';
        }
        echo '</div>';
        unset($_SESSION['errors']); // Clear errors after displaying
    }

    // Display success message from registration or other actions
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
        unset($_SESSION['success_message']);
    }
    ?>

    <form id="login-form" action="/src/controllers/login_controller.php" method="POST">
        <?php echo csrf_input(); ?>
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_SESSION['old_input']['username'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

<?php
// Clear old input data after displaying the form
if (isset($_SESSION['old_input'])) {
    unset($_SESSION['old_input']);
}

require_once __DIR__ . '/../templates/layout/footer.php';
?>

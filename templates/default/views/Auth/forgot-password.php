<div class="card">
    <div class="page-header">
        <h1>Reset Password</h1>
    </div>
    <p>Please enter your email address to receive a password reset link.</p>

    <?php $success = \App\Core\Session::getFlash('success'); ?>
    <?php if ($success): ?>
        <div class="notice success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php $error = \App\Core\Session::getFlash('error'); ?>
    <?php if ($error): ?>
        <div class="notice error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="<?= url('forgot-password') ?>" method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required>
        </div>
        <button type="submit" class="btn">Send Reset Link</button>
    </form>
    <div class="form-footer">
        <a href="<?= url('login') ?>">Remember your password? Login</a>
    </div>
</div>

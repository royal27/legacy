<div class="card" style="max-width: 400px; margin: 40px auto;">
    <h1 style="text-align: center;">Login</h1>

    <?php $error = \App\Core\Session::getFlash('error'); ?>
    <?php if ($error): ?>
        <div class="notice error" style="margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="/login" method="POST">
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" name="username" id="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <a href="/forgot-password">Forgot Password?</a>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

<style>
/* Scoped styles for form elements, can be moved to main CSS */
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}
input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    box-sizing: border-box;
}
.btn {
    display: block;
    width: 100%;
    padding: 12px;
    background: var(--primary-color);
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    transition: all 0.3s ease;
}
.btn:hover {
    opacity: 0.9;
}
.notice.error {
    padding: 15px;
    border-radius: 8px;
    border: 1px solid var(--accent-color);
    background-color: rgba(255, 69, 0, 0.2);
    color: #fff;
    text-align: center;
}
</style>

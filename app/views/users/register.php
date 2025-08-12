<!-- Header is loaded automatically by the controller -->

<div style="max-width: 400px; margin: 2rem auto;">
    <h1 class="gradient-text"><?php echo $data['title']; ?></h1>
    <p>Create an account to get started.</p>
    <form action="/users/register" method="post">
        <!-- Error handling would go here -->

        <label for="username">Username: <sup>*</sup></label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>

        <label for="email">Email: <sup>*</sup></label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>

        <label for="password">Password: <sup>*</sup></label>
        <input type="password" name="password" id="password" required>

        <label for="confirm_password">Confirm Password: <sup>*</sup></label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit" class="btn" style="width:100%;">Register</button>
        <p style="text-align:center;">
            <a href="/users/login">Have an account? Login</a>
        </p>
    </form>
</div>

<!-- Footer is loaded automatically -->

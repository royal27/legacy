<!-- Header is loaded automatically by the controller -->

<div style="max-width: 400px; margin: 2rem auto;">
    <h1 class="gradient-text"><?php echo $data['title']; ?></h1>
    <p>Please fill in your credentials to log in.</p>
    <form action="/users/login" method="post">
        <?php if(!empty($data['error'])): ?>
            <p style="color: red;"><?php echo $data['error']; ?></p>
        <?php endif; ?>

        <label for="email">Email: <sup>*</sup></label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>

        <label for="password">Password: <sup>*</sup></label>
        <input type="password" name="password" id="password" required>

        <button type="submit" class="btn" style="width:100%;">Login</button>
        <p style="text-align:center;">
            <a href="/users/register">No account? Register</a>
        </p>
    </form>
</div>

<!-- Footer is loaded automatically -->

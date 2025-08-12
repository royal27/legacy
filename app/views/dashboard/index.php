<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p>Welcome, <strong><?php echo htmlspecialchars($data['user']['username']); ?></strong>!</p>
<p>This is your personal dashboard. More features will be added here soon.</p>

<p>
    Your role is: <strong><?php echo htmlspecialchars($data['user']['role_name']); ?></strong>
</p>

<?php if (Auth::check('admin.access')) : ?>
    <p>You have access to the <a href="/admin">Admin Panel</a>.</p>
<?php endif; ?>

<a href="/users/logout" class="btn">Logout</a>

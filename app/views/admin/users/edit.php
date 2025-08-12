<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p>Edit user details and role. Leave the password field blank to keep the current password.</p>

<form action="/admin/users/edit/<?php echo $data['user']['id']; ?>" method="post" style="max-width: 500px;">
    <?php if (!empty($data['errors'])): ?>
        <div style="color: red; margin-bottom: 1rem;">
            <?php foreach ($data['errors'] as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <label for="username">Username: <sup>*</sup></label>
    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($data['user']['username']); ?>" required>

    <label for="email">Email: <sup>*</sup></label>
    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($data['user']['email']); ?>" required>

    <label for="password">New Password:</label>
    <input type="password" name="password" id="password" placeholder="Leave blank to keep current password">

    <label for="role_id">Role: <sup>*</sup></label>
    <select name="role_id" id="role_id" required>
        <?php foreach($data['roles'] as $role): ?>
            <option value="<?php echo $role['id']; ?>" <?php echo ($role['id'] == $data['user']['role_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($role['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn" style="margin-top: 1rem;">Update User</button>
    <a href="/admin/users" style="margin-left: 1rem;">Cancel</a>
</form>

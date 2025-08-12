<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p>Create a new user account and assign a role.</p>

<form action="/admin/users/add" method="post" style="max-width: 500px;">
    <?php if (!empty($data['errors'])): ?>
        <div style="color: red; margin-bottom: 1rem;">
            <?php foreach ($data['errors'] as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <label for="username">Username: <sup>*</sup></label>
    <input type="text" name="username" id="username" required>

    <label for="email">Email: <sup>*</sup></label>
    <input type="email" name="email" id="email" required>

    <label for="password">Password: <sup>*</sup></label>
    <input type="password" name="password" id="password" required>

    <label for="role_id">Role: <sup>*</sup></label>
    <select name="role_id" id="role_id" required>
        <?php foreach($data['roles'] as $role): ?>
            <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn" style="margin-top: 1rem;">Create User</button>
    <a href="/admin/users" style="margin-left: 1rem;">Cancel</a>
</form>

<?php
// A simple, standalone script to generate a password hash for the application.
// This file should be deleted from the server after use.

$hash = '';
if (!empty($_POST['password'])) {
    // Generate the hash using the default algorithm (currently bcrypt).
    // This is the same method used by password_verify() in the login script.
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Hash Generator</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 700px; margin: auto; color: #333; }
        h1, h2 { color: #111; }
        .container { border: 1px solid #ddd; padding: 20px; border-radius: 8px; background-color: #f9f9f9; }
        .hash-result { background-color: #e9ecef; padding: 15px; border: 1px solid #ccc; border-radius: 5px; word-wrap: break-word; margin-top: 20px; font-family: "Courier New", Courier, monospace; }
        .warning { color: #9f6000; background-color: #feefb3; padding: 15px; border: 1px solid #9f6000; border-radius: 5px; margin-bottom: 20px; }
        label { font-weight: bold; }
        input[type="text"] { width: calc(100% - 120px); padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 18px; border: none; background-color: #007bff; color: white; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        form { display: flex; align-items: center; gap: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Hash Generator</h1>
        <p class="warning"><b>Important:</b> For security reasons, please delete this file from your server after you have finished using it.</p>
        <p>Enter a new password below to generate a secure hash. You will use this hash to update your user account in the database.</p>

        <form action="create_password_hash.php" method="post">
            <label for="password">Password:</label>
            <input type="text" id="password" name="password" size="50" autofocus>
            <button type="submit">Generate Hash</button>
        </form>

        <?php if ($hash): ?>
            <h2>Generated Hash:</h2>
            <div class="hash-result">
                <p>1. Copy the entire hash string below:</p>
                <pre><strong><?php echo htmlspecialchars($hash); ?></strong></pre>
                <p>2. Open your database, find the <code>wgn_users</code> table (the prefix might be different based on your <code>core/config.php</code>), and locate your user.</p>
                <p>3. Replace the current value in the <code>password</code> column with this new hash.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

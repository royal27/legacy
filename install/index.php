<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Installer</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 5px; }
        h1 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 3px; box-sizing: border-box; }
        .btn { display: inline-block; background: #28a745; color: #fff; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; width: 100%; text-align: center; }
        .btn:hover { background: #218838; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 3px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 3px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Website Installer</h1>
        <?php
        // Check if config file already exists
        if (file_exists('../config.php')) {
            echo '<p class="error">The website is already installed. For security reasons, please delete the \'install\' folder.</p>';
        } else {
        ?>
        <form action="install.php" method="post">
            <h2>Database Details</h2>
            <div class="form-group">
                <label for="db_host">Database Host</label>
                <input type="text" id="db_host" name="db_host" value="localhost" required>
            </div>
            <div class="form-group">
                <label for="db_name">Database Name</label>
                <input type="text" id="db_name" name="db_name" required>
            </div>
            <div class="form-group">
                <label for="db_user">Database User</label>
                <input type="text" id="db_user" name="db_user" required>
            </div>
            <div class="form-group">
                <label for="db_pass">Database Password</label>
                <input type="password" id="db_pass" name="db_pass">
            </div>

            <h2>Admin Account</h2>
            <div class="form-group">
                <label for="admin_user">Admin Username</label>
                <input type="text" id="admin_user" name="admin_user" required>
            </div>
             <div class="form-group">
                <label for="admin_email">Admin Email</label>
                <input type="text" id="admin_email" name="admin_email" required>
            </div>
            <div class="form-group">
                <label for="admin_pass">Admin Password</label>
                <input type="password" id="admin_pass" name="admin_pass" required>
            </div>

            <button type="submit" class="btn">Install Now</button>
        </form>
        <?php } ?>
    </div>
</body>
</html>

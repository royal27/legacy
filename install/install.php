<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Progress</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 5px; }
        h1 { text-align: center; color: #333; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 3px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 3px; margin-bottom: 20px; }
        a { color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Installation Progress</h1>
        <?php

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die('<p class="error">Installation must be started from the installer form.</p>');
        }

        // --- 1. Get Form Data ---
        $db_host = $_POST['db_host'];
        $db_name = $_POST['db_name'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_pass'];
        $admin_user = $_POST['admin_user'];
        $admin_email = $_POST['admin_email'];
        $admin_pass = $_POST['admin_pass'];

        // Basic validation
        if (empty($db_host) || empty($db_name) || empty($db_user) || empty($admin_user) || empty($admin_email) || empty($admin_pass)) {
            die('<p class="error">Please fill in all required fields.</p>');
        }

        // --- 2. Create Config File Content ---
        $config_content = "<?php\n\n";
        $config_content .= "// --- Database Credentials ---\n";
        $config_content .= "define('DB_HOST', '" . addslashes($db_host) . "');\n";
        $config_content .= "define('DB_NAME', '" . addslashes($db_name) . "');\n";
        $config_content .= "define('DB_USER', '" . addslashes($db_user) . "');\n";
        $config_content .= "define('DB_PASS', '" . addslashes($db_pass) . "');\n\n";
        $config_content .= "// --- Site Settings ---\n";
        $site_url_value = rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . str_replace('/install/install.php', '', $_SERVER['SCRIPT_NAME']), '/');
        $config_content .= "define('SITE_URL', '" . addslashes($site_url_value) . "');\n";
        $config_content .= "define('BASE_PATH', __DIR__);\n";

        // --- 3. Test Database Connection ---
        $mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($mysqli->connect_error) {
            die('<p class="error">Database Connection Failed: ' . $mysqli->connect_error . '. Please check your credentials and try again.</p>');
        }

        // --- 4. Write Config File ---
        if (!@file_put_contents('../config.php', $config_content)) {
            die('<p class="error">Error: Could not write to config.php. Please check file permissions.</p>');
        }
        echo '<p class="success">Successfully created config.php file.</p>';

        // --- 5. Create Database Tables ---
        $sql = file_get_contents('install.sql'); // Use the separate SQL file

        if (!$mysqli->multi_query($sql)) {
            die('<p class="error">Error creating database tables: ' . $mysqli->error . '</p>');
        }
        // Clear multi_query results
        while ($mysqli->next_result()) {;}
        echo '<p class="success">Successfully created database tables.</p>';

        // --- 6. Create Admin User ---
        $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO `users` (username, email, password, role_id, is_validated, points) VALUES (?, ?, ?, 1, 1, 0)");
        $stmt->bind_param("sss", $admin_user, $admin_email, $hashed_password);
        if (!$stmt->execute()) {
             die('<p class="error">Error creating admin user: ' . $stmt->error . '</p>');
        }
        $stmt->close();
        echo '<p class="success">Successfully created admin account.</p>';

        $mysqli->close();

        // --- 7. Success Message ---
        echo '<h2>Installation Complete!</h2>';
        echo '<p class="success">Your website has been installed successfully.</p>';
        echo '<p><strong>Admin Username:</strong> ' . htmlspecialchars($admin_user) . '</p>';
        echo '<p><strong>Admin Password:</strong> The one you entered during installation.</p>';
        echo '<p class="error"><strong>IMPORTANT:</strong> For security reasons, please delete the "install" folder from your server immediately!</p>';
        echo '<p><a href="../index.php">Go to your website</a></p>';

        ?>
    </div>
</body>
</html>

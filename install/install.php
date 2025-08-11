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
        $config_content .= "define('SITE_URL', (isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . \$_SERVER['HTTP_HOST']);\n";
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
        $sql = "
        CREATE TABLE `settings` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `value` text NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        INSERT INTO `settings` (`name`, `value`) VALUES
        ('site_title', 'My Awesome Website'),
        ('logo_type', 'text'),
        ('logo_text', 'MyLogo'),
        ('logo_image', ''),
        ('footer_text', '© 2024 My Awesome Website. All rights reserved.'),
        ('default_lang', '1');

        CREATE TABLE `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(50) NOT NULL,
          `email` varchar(100) NOT NULL,
          `password` varchar(255) NOT NULL,
          `role_id` int(11) DEFAULT 2,
          `is_validated` tinyint(1) DEFAULT 0,
          `is_muted` tinyint(1) DEFAULT 0,
          `muted_until` datetime DEFAULT NULL,
          `is_banned` tinyint(1) DEFAULT 0,
          `banned_until` datetime DEFAULT NULL,
          `suspended_until` datetime DEFAULT NULL,
          `force_logout` tinyint(1) NOT NULL DEFAULT 0,
          `points` int(11) DEFAULT 0,
          `profile_picture` varchar(255) DEFAULT NULL,
          `last_login_points_awarded` date DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `username` (`username`),
          UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE `roles` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(50) NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        INSERT INTO `roles` (`id`, `name`) VALUES (1, 'Admin'), (2, 'User');

        CREATE TABLE `permissions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL COMMENT 'e.g., manage_settings, manage_users',
          `description` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
        (1, 'admin_login', 'Access the admin panel'),
        (2, 'manage_settings', 'Manage site settings'),
        (3, 'manage_users', 'Manage all users'),
        (4, 'manage_roles', 'Manage roles and permissions'),
        (5, 'manage_languages', 'Manage languages and translations'),
        (6, 'manage_plugins', 'Manage plugins');

        CREATE TABLE `role_permissions` (
          `role_id` int(11) NOT NULL,
          `permission_id` int(11) NOT NULL,
          PRIMARY KEY (`role_id`,`permission_id`),
          KEY `role_id` (`role_id`),
          KEY `permission_id` (`permission_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        -- Give Admin all permissions by default
        INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
        (1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6);


        CREATE TABLE `languages` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(50) NOT NULL,
          `code` varchar(10) NOT NULL,
          `is_active` tinyint(1) NOT NULL DEFAULT 1,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        INSERT INTO `languages` (`id`, `name`, `code`, `is_active`) VALUES (1, 'English', 'en', 1);

        CREATE TABLE `language_strings` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `lang_id` int(11) NOT NULL,
          `lang_key` varchar(100) NOT NULL,
          `lang_value` text NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `lang_id_key` (`lang_id`,`lang_key`),
          KEY `lang_id` (`lang_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        INSERT INTO `language_strings` (`lang_id`, `lang_key`, `lang_value`) VALUES
        (1, 'welcome_message', 'Welcome to our Website!'),
        (1, 'homepage_content', 'This is the main content area. We will build exciting things here soon.'),
        (1, 'site_not_installed_title', 'Site Not Installed'),
        (1, 'site_not_installed_message', 'It looks like the configuration file is missing. Your website is not yet installed or configured.'),
        (1, 'go_to_installer_button', 'Go to Installer'),
        (1, 'home_menu_link', 'Home'),
        (1, 'about_menu_link', 'About'),
        (1, 'contact_menu_link', 'Contact');

        CREATE TABLE `pages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `slug` VARCHAR(255) NOT NULL,
            `content` TEXT,
            `in_footer` BOOLEAN DEFAULT FALSE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE `menu_items` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(100) NOT NULL,
          `url` varchar(255) NOT NULL,
          `target` varchar(20) NOT NULL DEFAULT '_self',
          `menu_location` varchar(50) NOT NULL DEFAULT 'main_nav',
          `sort_order` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        INSERT INTO `menu_items` (`id`, `title`, `url`, `target`, `menu_location`, `sort_order`) VALUES
        (1, 'Home', 'index.php', '_self', 'main_nav', 1),
        (2, 'GitHub', 'https://github.com', '_blank', 'main_nav', 2);

        CREATE TABLE `banned_ips` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `ip_address` varchar(45) NOT NULL,
          `reason` varchar(255) DEFAULT NULL,
          `banned_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `ip_address` (`ip_address`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE `plugins` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `identifier` varchar(100) NOT NULL COMMENT 'Unique string from plugin.json',
          `name` varchar(255) NOT NULL,
          `version` varchar(50) DEFAULT NULL,
          `is_active` tinyint(1) NOT NULL DEFAULT 0,
          `custom_link` varchar(255) DEFAULT NULL,
          `installed_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `identifier` (`identifier`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

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

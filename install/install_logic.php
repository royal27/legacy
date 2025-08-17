<?php

function create_config_file($db_details) {
    $config_content = "<?php\n\n";
    $config_content .= "/* --- Database Configuration --- */\n";
    $config_content .= "define('DB_HOST', '" . addslashes($db_details['db_host']) . "');\n";
    $config_content .= "define('DB_USER', '" . addslashes($db_details['db_user']) . "');\n";
    $config_content .= "define('DB_PASS', '" . addslashes($db_details['db_pass']) . "');\n";
    $config_content .= "define('DB_NAME', '" . addslashes($db_details['db_name']) . "');\n";
    $config_content .= "define('DB_PREFIX', '" . addslashes($db_details['db_prefix']) . "');\n\n";

    $config_content .= "/* --- Site Settings --- */\n";
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(str_replace('/install', '', dirname($_SERVER['PHP_SELF'])), '/');
    $site_url = "{$protocol}://{$host}{$path}";
    $config_content .= "define('SITE_URL', '" . addslashes($site_url) . "');\n";
    $config_content .= "define('INSTALLED', true);\n";

    $config_path = dirname(__DIR__) . '/config/config.php';

    if (file_put_contents($config_path, $config_content)) {
        return true;
    }
    return false;
}

function create_database_tables($db_details, $founder_details, $activate_plugins = []) {
    $mysqli = new mysqli($db_details['db_host'], $db_details['db_user'], $db_details['db_pass'], $db_details['db_name']);
    if ($mysqli->connect_error) {
        return "Database connection failed: " . $mysqli->connect_error;
    }

    $prefix = $mysqli->real_escape_string($db_details['db_prefix']);

    $sql = "
    CREATE TABLE IF NOT EXISTS `{$prefix}users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(50) NOT NULL,
      `email` varchar(100) NOT NULL,
      `password` varchar(255) NOT NULL,
      `role_id` int(11) NOT NULL,
      `points` int(11) NOT NULL DEFAULT '0',
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `username` (`username`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}roles` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(50) NOT NULL,
      `permissions` text,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}settings` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `value` text,
      PRIMARY KEY (`id`),
      UNIQUE KEY `name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}languages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL,
        `code` varchar(10) NOT NULL,
        `is_default` tinyint(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}permissions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `permission_key` varchar(100) NOT NULL,
      `description` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `permission_key` (`permission_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}role_permissions` (
      `role_id` int(11) NOT NULL,
      `permission_id` int(11) NOT NULL,
      PRIMARY KEY (`role_id`,`permission_id`),
      KEY `permission_id` (`permission_id`),
      CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `{$prefix}roles` (`id`) ON DELETE CASCADE,
      CONSTRAINT `fk_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `{$prefix}permissions` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}plugins` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `directory_name` varchar(100) NOT NULL,
      `is_active` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      UNIQUE KEY `directory_name` (`directory_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}forums` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `parent_id` int(11) DEFAULT NULL,
      `name` varchar(255) NOT NULL,
      `description` text,
      `sort_order` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `parent_id` (`parent_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}topics` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `forum_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `last_post_at` datetime DEFAULT NULL,
      `last_post_user_id` int(11) DEFAULT NULL,
      `is_locked` tinyint(1) NOT NULL DEFAULT '0',
      `is_sticky` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `forum_id` (`forum_id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}posts` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `topic_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `content` text NOT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `topic_id` (`topic_id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}chat_rooms` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `description` text,
      `is_private` tinyint(1) NOT NULL DEFAULT '0',
      `created_by_user_id` int(11) NOT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}chat_messages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `room_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `content` text NOT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `room_id` (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}chat_room_members` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `room_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `room_user` (`room_id`,`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}tickets` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `status` varchar(50) NOT NULL DEFAULT 'Open',
      `priority` varchar(50) NOT NULL DEFAULT 'Normal',
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `last_updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}ticket_replies` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `ticket_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `content` text NOT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `ticket_id` (`ticket_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}download_categories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `description` text,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}download_files` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `category_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `description` text,
      `filename` varchar(255) NOT NULL,
      `filepath` varchar(255) NOT NULL,
      `filesize` int(11) NOT NULL,
      `download_count` int(11) NOT NULL DEFAULT '0',
      `is_validated` tinyint(1) NOT NULL DEFAULT '0',
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `category_id` (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}gallery_albums` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `name` varchar(255) NOT NULL,
      `description` text,
      `privacy_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=public, 1=private, 2=password_protected',
      `access_code` varchar(255) DEFAULT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}gallery_photos` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `album_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `title` varchar(255) DEFAULT NULL,
      `description` text,
      `filename` varchar(255) NOT NULL,
      `filepath` varchar(255) NOT NULL,
      `is_validated` tinyint(1) NOT NULL DEFAULT '0',
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `album_id` (`album_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}alerts` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `sent_by_user_id` int(11) NOT NULL,
      `content` text NOT NULL,
      `is_read` tinyint(1) NOT NULL DEFAULT '0',
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `{$prefix}tasks` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `assigned_to_user_id` int(11) NOT NULL,
      `created_by_user_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `description` text,
      `status` varchar(50) NOT NULL DEFAULT 'To Do',
      `due_date` date DEFAULT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `assigned_to_user_id` (`assigned_to_user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    if (!$mysqli->multi_query($sql)) {
        return "Error creating tables: " . $mysqli->error;
    }

    // Clear results of multi_query
    while ($mysqli->next_result()) {
        if ($res = $mysqli->store_result()) {
            $res->free();
        }
    }

    // Insert default roles
    $mysqli->query("INSERT INTO `{$prefix}roles` (`id`, `name`, `permissions`) VALUES (1, 'Founder', '*'), (2, 'User', NULL);");

    // Insert founder account
    $username = $mysqli->real_escape_string($founder_details['username']);
    $email = $mysqli->real_escape_string($founder_details['email']);
    $password = password_hash($founder_details['password'], PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO `{$prefix}users` (`username`, `email`, `password`, `role_id`) VALUES (?, ?, ?, 1)");
    $stmt->bind_param('sss', $username, $email, $password);
    if (!$stmt->execute()) {
        return "Error creating founder account: " . $stmt->error;
    }
    $stmt->close();

    // Insert default languages
    $mysqli->query("INSERT INTO `{$prefix}languages` (`name`, `code`, `is_default`) VALUES ('English', 'en', 1), ('Română', 'ro', 0);");

    // Insert initial settings
    $site_name = 'My Awesome Project';
    $mysqli->query("INSERT INTO `{$prefix}settings` (`name`, `value`) VALUES ('site_name', '{$site_name}');");

    // Insert default permissions
    $permissions = [
        ['users.view', 'View the user list'],
        ['users.create', 'Create new users'],
        ['users.edit', 'Edit existing users'],
        ['users.delete', 'Delete users'],
        ['roles.view', 'View the role list'],
        ['roles.create', 'Create new roles'],
        ['roles.edit', 'Edit roles and their permissions'],
        ['roles.delete', 'Delete roles'],
        ['settings.edit', 'Edit site settings'],
        ['permissions.manage', 'Manage roles and permissions (meta-permission)']
    ];

    $stmt = $mysqli->prepare("INSERT INTO `{$prefix}permissions` (permission_key, description) VALUES (?, ?)");
    foreach ($permissions as $permission) {
        $stmt->bind_param('ss', $permission[0], $permission[1]);
        $stmt->execute();
    }
    $stmt->close();

    // Activate selected plugins
    if (!empty($activate_plugins)) {
        $stmt = $mysqli->prepare("INSERT INTO `{$prefix}plugins` (directory_name, is_active) VALUES (?, 1) ON DUPLICATE KEY UPDATE is_active = 1");
        foreach ($activate_plugins as $plugin_dir) {
            $stmt->bind_param('s', $plugin_dir);
            $stmt->execute();
        }
        $stmt->close();
    }

    $mysqli->close();
    return true;
}
?>

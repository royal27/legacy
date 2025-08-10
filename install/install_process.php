<?php
// Enable full error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
define('DS', DIRECTORY_SEPARATOR);

// --- Security Check ---
$required_sessions = ['language', 'db_host', 'db_user', 'db_pass', 'db_name', 'db_prefix', 'founder_user', 'founder_email', 'founder_pass'];
foreach ($required_sessions as $key) {
    if (!isset($_SESSION[$key])) {
        die("Session data is missing. Please start the installation from the beginning.");
    }
}

// --- Installation Variables ---
$db_host = $_SESSION['db_host'];
$db_user = $_SESSION['db_user'];
$db_pass = $_SESSION['db_pass'];
$db_name = $_SESSION['db_name'];
$db_prefix = $_SESSION['db_prefix'];
$language = $_SESSION['language'];
$founder_user = $_SESSION['founder_user'];
$founder_email = $_SESSION['founder_email'];
$founder_pass = password_hash($_SESSION['founder_pass'], PASSWORD_DEFAULT);

// --- Database Connection Test ---
$mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die("Database Connection Failed: " . $mysqli->connect_error . "<br><a href='index.php'>Go Back</a>");
}

// --- 1. Create config.php file ---
$config_content = "<?php" . PHP_EOL .
"// Database credentials" . PHP_EOL .
"define('DB_HOST', '{$db_host}');" . PHP_EOL .
"define('DB_USER', '{$db_user}');" . PHP_EOL .
"define('DB_PASS', '{$db_pass}');" . PHP_EOL .
"define('DB_NAME', '{$db_name}');" . PHP_EOL .
"define('DB_PREFIX', '{$db_prefix}');" . PHP_EOL .
"?>";
$config_path = __DIR__ . DS . '..' . DS . 'includes' . DS . 'config.php';
if (!file_put_contents($config_path, $config_content)) {
    die("Error: Could not write to config.php. Please check file permissions. <br><a href='index.php'>Go Back</a>");
}

// --- 2. SQL Table Definitions ---
$sql_commands = [
    "CREATE TABLE `{$db_prefix}users` (`id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(50) NOT NULL, `password` varchar(255) NOT NULL, `email` varchar(100) NOT NULL, `role` varchar(20) NOT NULL DEFAULT 'user', `language` varchar(5) NOT NULL DEFAULT '{$language}', `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `username` (`username`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE `{$db_prefix}settings` (`name` varchar(50) NOT NULL, `value` text, PRIMARY KEY (`name`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE `{$db_prefix}languages` (`id` int(11) NOT NULL AUTO_INCREMENT, `code` varchar(5) NOT NULL, `name` varchar(50) NOT NULL, `is_default` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`), UNIQUE KEY `code` (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE `{$db_prefix}translations` (`id` int(11) NOT NULL AUTO_INCREMENT, `lang_code` varchar(5) NOT NULL, `translation_key` varchar(100) NOT NULL, `translation_value` text NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `lang_key_unique` (`lang_code`,`translation_key`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE `{$db_prefix}templates` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, `directory` varchar(100) NOT NULL, `is_active` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`), UNIQUE KEY `directory` (`directory`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE `{$db_prefix}links` (`id` int(11) NOT NULL AUTO_INCREMENT, `url` varchar(255) NOT NULL, `target` varchar(20) DEFAULT '_self', `sort_order` int(11) DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE `{$db_prefix}link_translations` (`id` int(11) NOT NULL AUTO_INCREMENT, `link_id` int(11) NOT NULL, `lang_code` varchar(5) NOT NULL, `link_name` varchar(100) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `link_lang_unique` (`link_id`,`lang_code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE `{$db_prefix}plugins` (`id` int(11) NOT NULL AUTO_INCREMENT, `directory_name` varchar(100) NOT NULL, `is_active` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`), UNIQUE KEY `directory_name` (`directory_name`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

foreach ($sql_commands as $sql) {
    if (!$mysqli->query($sql)) {
        die("Error creating table: " . $mysqli->error . "<br><a href='index.php'>Go Back</a>");
    }
}

// --- 4. Insert Default Data ---
$sql_data = [
    "INSERT INTO `{$db_prefix}users` (username, password, email, role, language) VALUES ('{$founder_user}', '{$founder_pass}', '{$founder_email}', 'founder', '{$language}');",
    "INSERT INTO `{$db_prefix}languages` (code, name, is_default) VALUES ('en', 'English', " . ($language == 'en' ? 1 : 0) . "), ('ro', 'Română', " . ($language == 'ro' ? 1 : 0) . ");",
    "INSERT INTO `{$db_prefix}settings` (name, value) VALUES ('site_name', 'My Awesome Website'), ('default_template', 'default');",
    "INSERT INTO `{$db_prefix}templates` (name, directory, is_active) VALUES ('Default Template', 'default', 1);"
];

foreach ($sql_data as $sql) {
    if (!$mysqli->query($sql)) {
        die("Error inserting default data: " . $mysqli->error . "<br><a href='index.php'>Go Back</a>");
    }
}
$mysqli->close();
session_destroy();

// --- 5. Cleanup ---
function delete_install_dir($dirPath) {
    if (!is_dir($dirPath)) return;
    $iterator = new DirectoryIterator($dirPath);
    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isDot()) continue;
        if ($fileinfo->isDir()) {
            delete_install_dir($fileinfo->getRealPath());
        } else {
            unlink($fileinfo->getRealPath());
        }
    }
    rmdir($dirPath);
}
try {
    delete_install_dir(__DIR__);
} catch (Exception $e) { /* Ignore potential errors */ }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Installation Complete</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0; padding: 20px; color: #fff;
            background-image: linear-gradient(135deg, violet, blue, red);
            background-attachment: fixed; background-size: cover;
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
        }
        .container {
            max-width: 600px; width: 100%;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 40px; border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-align: center;
        }
        h1 { color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        a {
            display: inline-block; padding: 12px 25px; border: none;
            background-image: linear-gradient(135deg, #8e2de2, #4a00e0);
            color: white; font-size: 16px; font-weight: bold;
            border-radius: 5px; cursor: pointer;
            transition: all 0.3s; text-decoration: none; margin-top: 20px;
        }
        a:hover { box-shadow: 0 0 15px rgba(255,255,255,0.5); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Installation Successful!</h1>
        <p>Your website has been installed correctly.</p>
        <p><strong>For security reasons, the installation directory should now be gone. If it is not, please delete it manually.</strong></p>
        <a href="../index.php">Go to Homepage</a>
    </div>
</body>
</html>

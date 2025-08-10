<?php
// Start the session to access stored data
session_start();

// --- Security Check ---
// Verify that we are coming from step 3 and all data is present
$required_sessions = [
    'language', 'db_host', 'db_user', 'db_pass', 'db_name', 'db_prefix',
    'founder_user', 'founder_email', 'founder_pass'
];

foreach ($required_sessions as $key) {
    if (!isset($_SESSION[$key])) {
        // If any data is missing, abort and redirect to the start
        header('Location: index.php');
        exit;
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
$config_content = "<?php
// Database credentials
define('DB_HOST', '{$db_host}');
define('DB_USER', '{$db_user}');
define('DB_PASS', '{$db_pass}');
define('DB_NAME', '{$db_name}');
define('DB_PREFIX', '{$db_prefix}');

// Other settings
define('SITE_URL', 'http://' . \$_SERVER['HTTP_HOST']); // Adjust if using https
?>";

$config_path = __DIR__ . '/../includes/config.php';
if (!file_put_contents($config_path, $config_content)) {
    die("Error: Could not write to config.php. Please check file permissions. <br><a href='index.php'>Go Back</a>");
}

// --- 2. SQL Table Definitions ---
$sql = "
CREATE TABLE `{$db_prefix}users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `language` varchar(5) NOT NULL DEFAULT '{$language}',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{$db_prefix}settings` (
  `name` varchar(50) NOT NULL,
  `value` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{$db_prefix}languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{$db_prefix}translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_code` varchar(5) NOT NULL,
  `translation_key` varchar(100) NOT NULL,
  `translation_value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang_key_unique` (`lang_code`,`translation_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{$db_prefix}templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `directory` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `directory` (`directory`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{$db_prefix}links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `target` varchar(20) DEFAULT '_self',
  `sort_order` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{$db_prefix}link_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_id` int(11) NOT NULL,
  `lang_code` varchar(5) NOT NULL,
  `link_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link_lang_unique` (`link_id`,`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{$db_prefix}plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `directory_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `directory_name` (`directory_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// --- 3. Execute SQL ---
if (!$mysqli->multi_query($sql)) {
    die("Error creating tables: " . $mysqli->error . "<br><a href='index.php'>Go Back</a>");
}
// Clear results of multi_query
while ($mysqli->more_results() && $mysqli->next_result()) {;}

// --- 4. Insert Default Data ---
$sql_data = "
-- Insert Founder User
INSERT INTO `{$db_prefix}users` (username, password, email, role, language) VALUES ('{$founder_user}', '{$founder_pass}', '{$founder_email}', 'founder', '{$language}');

-- Insert Default Languages
INSERT INTO `{$db_prefix}languages` (code, name, is_default) VALUES ('en', 'English', " . ($language == 'en' ? 1 : 0) . "), ('ro', 'Română', " . ($language == 'ro' ? 1 : 0) . ");

-- Insert Default Settings
INSERT INTO `{$db_prefix}settings` (name, value) VALUES ('site_name', 'My Awesome Website'), ('default_template', 'default');

-- Insert Default Template
INSERT INTO `{$db_prefix}templates` (name, directory, is_active) VALUES ('Default Template', 'default', 1);
";

if (!$mysqli->multi_query($sql_data)) {
    die("Error inserting default data: " . $mysqli->error . "<br><a href='index.php'>Go Back</a>");
}
while ($mysqli->more_results() && $mysqli->next_result()) {;}

$mysqli->close();

// --- 5. Cleanup ---
// Destroy session data
session_destroy();

// Function to recursively delete the install directory
function delete_install_dir($dir_path) {
    if (!is_dir($dir_path)) {
        return;
    }
    $files = glob($dir_path . '/*');
    foreach ($files as $file) {
        is_dir($file) ? delete_install_dir($file) : unlink($file);
    }
    rmdir($dir_path);
}

// Delete the directory this script is in
delete_install_dir(__DIR__);

// --- Final Success Message ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Installation Complete</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; color: #333; text-align: center; padding: 50px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #4cae4c; }
        a { background: #5cb85c; color: white; padding: 12px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 20px; }
        a:hover { background: #4cae4c; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Installation Successful!</h1>
        <p>Your website has been installed correctly.</p>
        <p><strong>For security reasons, the installation directory has been deleted.</strong></p>
        <a href="../index.php">Go to Homepage</a>
    </div>
</body>
</html>

<?php
// Temporary database patch script to add the plugins table.
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

echo "Running DB Patch for Plugins Table...\n";

$prefix = DB_PREFIX;
$sql = "
CREATE TABLE IF NOT EXISTS `{$prefix}plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `directory_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `directory_name` (`directory_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($mysqli->query($sql) === TRUE) {
    echo "Table '{$prefix}plugins' created successfully or already exists.\n";
} else {
    echo "Error creating table: " . $mysqli->error . "\n";
}

$mysqli->close();

// In a real scenario, you would delete this file after running it.
?>

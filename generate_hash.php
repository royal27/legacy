<?php
// Use this script to generate a new password hash.
// 1. Change the value of $passwordToHash to your desired password if you want something other than 'password'.
// 2. Run this script in your browser (e.g., http://localhost/project-folder/generate_hash.php).
// 3. Copy the generated hash.
// 4. Go to phpMyAdmin, open the 'users' table, edit the 'owner' user, and paste the new hash into the 'password' field.

$passwordToHash = 'password';

// Check if password_hash function exists
if (!function_exists('password_hash')) {
    die("The password_hash() function is not available. Please use a PHP version 5.5 or newer.");
}

$hash = password_hash($passwordToHash, PASSWORD_DEFAULT);

header('Content-Type: text/plain');
echo "This is a temporary utility script to generate a password hash.\n";
echo "Please delete this file (`generate_hash.php`) from your server after use.\n\n";
echo "========================================================================\n\n";
echo "Password to hash: " . htmlspecialchars($passwordToHash) . "\n\n";
echo "Generated Hash (copy this entire line):\n";
echo $hash;
?>

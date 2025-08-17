<?php
/**
 * DEBUG STEP 2: Test config loading
 */

define('ROOT_PATH', __DIR__);

$config_file = ROOT_PATH . '/config/config.php';

if (!file_exists($config_file)) {
    // This assumes the user has already installed.
    die('ERROR: config.php not found. Please ensure the installation was completed.');
}

require_once $config_file;

// Security check: ensure install directory is deleted after installation
if (defined('INSTALLED') && INSTALLED === true && is_dir(ROOT_PATH . '/install')) {
    die('<b>Security Warning!</b><br>Please delete the "install" directory immediately.');
}

echo 'Index OK - Step 2: Config loaded successfully.';

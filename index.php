<?php
/**
 * DEBUG STEP 3: Test core initializations
 */

define('ROOT_PATH', __DIR__);

// Load config
require_once ROOT_PATH . '/config/config.php';

// Security check
if (defined('INSTALLED') && INSTALLED === true && is_dir(ROOT_PATH . '/install')) {
    die('<b>Security Warning!</b><br>Please delete the "install" directory immediately.');
}

// Load helpers
require_once ROOT_PATH . '/src/Core/helpers.php';

// Autoloader for App\ namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Error and Exception handling
error_reporting(E_ALL);
set_error_handler('App\Core\Error::errorHandler');
set_exception_handler('App\Core\Error::exceptionHandler');

// Initialize Session
App\Core\Session::init();

// Load active plugins
$pluginManager = new App\Core\PluginManager();
$pluginManager->loadActivePlugins();

// Initialize core managers
App\Core\PointsManager::init();

echo 'Index OK - Step 3: Core loaded successfully.';

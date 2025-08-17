<?php
/**
 * Front Controller
 */

// Define root path for includes
define('ROOT_PATH', __DIR__);

// Check if the config file exists
$config_file = ROOT_PATH . '/config/config.php';

if (!file_exists($config_file)) {
    // If not installed, redirect to installer
    if (is_dir(ROOT_PATH . '/install')) {
        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]";
        $install_path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/install/';
        header('Location: ' . $base_url . $install_path);
        exit;
    } else {
        die('ERROR: Application is not installed and the installer directory is missing.');
    }
}

// Load the configuration
require_once $config_file;

// Security check: ensure install directory is deleted after installation
if (defined('INSTALLED') && INSTALLED === true && is_dir(ROOT_PATH . '/install')) {
    die('<b>Security Warning!</b><br>Please delete the "install" directory immediately.');
}

// Load helpers
require_once ROOT_PATH . '/src/Core/helpers.php';

// Autoloader
require_once ROOT_PATH . '/src/Core/Autoloader.php';
App\Core\Autoloader::register();
App\Core\Autoloader::addNamespace('App', ROOT_PATH . '/src');

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

// Routing
$router = new App\Core\Router();

// Core Application Routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Auth', 'action' => 'login', 'method' => 'GET']);
$router->add('login', ['controller' => 'Auth', 'action' => 'postLogin', 'method' => 'POST']);
$router->add('logout', ['controller' => 'Auth', 'action' => 'logout']);

// Admin Routes
$router->add('admin', ['controller' => 'Admin\\Dashboard', 'action' => 'index']);
$router->add('admin/dashboard', ['controller' => 'Admin\\Dashboard', 'action' => 'index']);
// User Management
$router->add('admin/users', ['controller' => 'Admin\\Users', 'action' => 'index', 'method' => 'GET']);
$router->add('admin/users/new', ['controller' => 'Admin\\Users', 'action' => 'new', 'method' => 'GET']);
$router->add('admin/users/create', ['controller' => 'Admin\\Users', 'action' => 'create', 'method' => 'POST']);
$router->add('admin/users/edit/{id:\d+}', ['controller' => 'Admin\\Users', 'action' => 'edit', 'method' => 'GET']);
$router->add('admin/users/update/{id:\d+}', ['controller' => 'Admin\\Users', 'action' => 'update', 'method' => 'POST']);
$router->add('admin/users/delete/{id:\d+}', ['controller' => 'Admin\\Users', 'action' => 'delete', 'method' => 'GET']);
// Role Management
$router->add('admin/roles', ['controller' => 'Admin\\Roles', 'action' => 'index', 'method' => 'GET']);
$router->add('admin/roles/new', ['controller' => 'Admin\\Roles', 'action' => 'new', 'method' => 'GET']);
$router->add('admin/roles/create', ['controller' => 'Admin\\Roles', 'action' => 'create', 'method' => 'POST']);
$router->add('admin/roles/edit/{id:\d+}', ['controller' => 'Admin\\Roles', 'action' => 'edit', 'method' => 'GET']);
$router->add('admin/roles/update/{id:\d+}', ['controller' => 'Admin\\Roles', 'action' => 'update', 'method' => 'POST']);
$router->add('admin/roles/delete/{id:\d+}', ['controller' => 'Admin\\Roles', 'action' => 'delete', 'method' => 'GET']);
// Plugin Management
$router->add('admin/plugins', ['controller' => 'Admin\\Plugins', 'action' => 'index', 'method' => 'GET']);
$router->add('admin/plugins/activate/{plugin_dir:[a-zA-Z0-9_-]+}', ['controller' => 'Admin\\Plugins', 'action' => 'activate', 'method' => 'GET']);
$router->add('admin/plugins/deactivate/{plugin_dir:[a-zA-Z0-9_-]+}', ['controller' => 'Admin\\Plugins', 'action' => 'deactivate', 'method' => 'GET']);

// Let plugins add their routes now
App\Core\Hooks::trigger('register_routes');

// Site Settings
$router->add('admin/settings', ['controller' => 'Admin\\Settings', 'action' => 'index', 'method' => 'GET']);
$router->add('admin/settings/update', ['controller' => 'Admin\\Settings', 'action' => 'update', 'method' => 'POST']);

// Generic routes - should be last
$router->add('{controller}/{action}');


// Dispatch the route
function dispatch() {
    $router = App\Core\Router::getInstance();
    $url = $_GET['route'] ?? '';

    if ($router->match($url)) {
        $params = $router->getParams();

        $controller_name = $params['controller'];
        if (strpos($controller_name, 'Plugins\\') === 0) {
            $controller = $controller_name; // It's a fully namespaced plugin controller
        } else {
            $controller = 'App\\Controllers\\' . $controller_name; // It's a core controller
        }

        if (class_exists($controller)) {
            $controller_object = new $controller($params);

            $action = $params['action'];

            if (is_callable([$controller_object, $action])) {
                $controller_object->$action();
                return true;
            }
        }
    }
    return false;
}

if (!dispatch()) {
    http_response_code(404);
    App\Core\View::render('Errors/404.php', ['title' => 'Page Not Found']);
}

?>

<?php
/**
 * Tasks Plugin Initializer
 */

// --- Autoloader ---
spl_autoload_register(function ($class) {
    $prefix = 'Plugins\\Tasks\\';
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


// --- Route Registration ---
function tasks_register_routes() {
    $router = \App\Core\Router::getInstance();

    // This is an admin-only feature
    $router->add('admin/tasks', ['controller' => 'Plugins\\Tasks\\Controllers\\Admin\\TaskController', 'action' => 'index']);
    $router->add('admin/tasks/new', ['controller' => 'Plugins\\Tasks\\Controllers\\Admin\\TaskController', 'action' => 'new']);
    $router->add('admin/tasks/create', ['controller' => 'Plugins\\Tasks\\Controllers\\Admin\\TaskController', 'action' => 'create', 'method' => 'POST']);
    $router->add('admin/tasks/edit/{id:\d+}', ['controller' => 'Plugins\\Tasks\\Controllers\\Admin\\TaskController', 'action' => 'edit']);
    $router->add('admin/tasks/update/{id:\d+}', ['controller' => 'Plugins\\Tasks\\Controllers\\Admin\\TaskController', 'action' => 'update', 'method' => 'POST']);
    $router->add('admin/tasks/delete/{id:\d+}', ['controller' => 'Plugins\\Tasks\\Controllers\\Admin\\TaskController', 'action' => 'delete']);
}
\App\Core\Hooks::listen('register_routes', 'tasks_register_routes');

// Register view paths
\App\Core\View::registerPluginViews('tasks_admin', __DIR__ . '/views/admin');


// --- Hook Registration ---
\App\Core\Hooks::listen('admin_sidebar_links', function() {
    // if (\App\Core\Auth::hasPermission('tasks.manage')) {
        echo '<a href="' . url('admin/tasks') . '">Tasks</a>';
    // }
});

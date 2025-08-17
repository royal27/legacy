<?php
/**
 * Forum Plugin Initializer
 */

// Register this plugin's namespace with the main autoloader
\App\Core\Autoloader::addNamespace('Plugins\\Forum', __DIR__ . '/src');


// --- Route Registration ---
function forum_register_routes() {
    $router = \App\Core\Router::getInstance();
    // Frontend routes
    $router->add('forum', ['controller' => 'Plugins\\Forum\\Controllers\\ForumController', 'action' => 'index']);
    $router->add('forum/topic/new/{id:\d+}', ['controller' => 'Plugins\\Forum\\Controllers\\TopicController', 'action' => 'new', 'method' => 'GET']);
    $router->add('forum/topic/create', ['controller' => 'Plugins\\Forum\\Controllers\\TopicController', 'action' => 'create', 'method' => 'POST']);
    $router->add('topic/{id:\d+}', ['controller' => 'Plugins\\Forum\\Controllers\\PostController', 'action' => 'show', 'method' => 'GET']);
    $router->add('topic/{id:\d+}/reply', ['controller' => 'Plugins\\Forum\\Controllers\\PostController', 'action' => 'reply', 'method' => 'POST']);

    // Admin routes
    $router->add('admin/forum', ['controller' => 'Plugins\\Forum\\Controllers\\Admin\\ForumController', 'action' => 'index']);
    $router->add('admin/forum/new', ['controller' => 'Plugins\\Forum\\Controllers\\Admin\\ForumController', 'action' => 'new']);
    $router->add('admin/forum/create', ['controller' => 'Plugins\\Forum\\Controllers\\Admin\\ForumController', 'action' => 'create', 'method' => 'POST']);
    $router->add('admin/forum/edit/{id:\d+}', ['controller' => 'Plugins\\Forum\\Controllers\\Admin\\ForumController', 'action' => 'edit']);
    $router->add('admin/forum/update/{id:\d+}', ['controller' => 'Plugins\\Forum\\Controllers\\Admin\\ForumController', 'action' => 'update', 'method' => 'POST']);
    $router->add('admin/forum/delete/{id:\d+}', ['controller' => 'Plugins\\Forum\\Controllers\\Admin\\ForumController', 'action' => 'delete']);
}
\App\Core\Hooks::listen('register_routes', 'forum_register_routes');

// Register the view directories for this plugin
\App\Core\View::registerPluginViews('forum_frontend', __DIR__ . '/views/frontend');
\App\Core\View::registerPluginViews('forum_admin', __DIR__ . '/views/admin');


// --- Hook Registration ---
// Add a link to the admin sidebar
\App\Core\Hooks::listen('admin_sidebar_links', function() {
    if (\App\Core\Auth::hasPermission('roles.view')) { // A real permission check
        echo '<a href="' . url('admin/forum') . '">Manage Forum</a>';
    }
});

// For now, just a confirmation that the plugin is loaded.
// echo "Forum Plugin Loaded!";

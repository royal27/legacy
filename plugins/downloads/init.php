<?php
/**
 * Downloads Plugin Initializer
 */

// Register this plugin's namespace with the main autoloader
\App\Core\Autoloader::addNamespace('Plugins\\Downloads', __DIR__ . '/src');


// --- Route Registration ---
function downloads_register_routes() {
    $router = \App\Core\Router::getInstance();

    // Frontend routes
    $router->add('downloads', ['controller' => 'Plugins\\Downloads\\Controllers\\Download', 'action' => 'index']);
    $router->add('downloads/category/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Download', 'action' => 'category']);
    $router->add('downloads/file/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Download', 'action' => 'file']);
    $router->add('downloads/go/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Download', 'action' => 'go']); // The actual download link
    $router->add('downloads/upload', ['controller' => 'Plugins\\Downloads\\Controllers\\Download', 'action' => 'upload', 'method' => 'GET']);
    $router->add('downloads/save', ['controller' => 'Plugins\\Downloads\\Controllers\\Download', 'action' => 'save', 'method' => 'POST']);

    // Admin routes
    $router->add('admin/downloads', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\Download', 'action' => 'index']);
    // Categories
    $router->add('admin/downloads/categories', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\Download', 'action' => 'categories']);
    $router->add('admin/downloads/categories/new', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\Download', 'action' => 'newCategory']);
    $router->add('admin/downloads/categories/create', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\Download', 'action' => 'createCategory', 'method' => 'POST']);
    $router->add('admin/downloads/categories/edit/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\Download', 'action' => 'editCategory']);
    $router->add('admin/downloads/categories/update/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\Download', 'action' => 'updateCategory', 'method' => 'POST']);
    $router->add('admin/downloads/categories/delete/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\Download', 'action' => 'deleteCategory']);
    // Files
    $router->add('admin/downloads/files', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\Download', 'action' => 'files']);
    $router->add('admin/downloads/files/validate/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\Download', 'action' => 'validateFile']);
    $router->add('admin/downloads/files/delete/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\Download', 'action' => 'deleteFile']);
}
\App\Core\Hooks::listen('register_routes', 'downloads_register_routes');


// --- Hook Registration ---
\App\Core\Hooks::listen('admin_sidebar_links', function() {
    // if (\App\Core\Auth::hasPermission('downloads.manage')) {
        echo '<a href="' . url('admin/downloads') . '">Downloads</a>';
    // }
});

\App\Core\Hooks::listen('main_nav_links', function() {
    echo '<a href="' . url('downloads') . '">Downloads</a>';
});

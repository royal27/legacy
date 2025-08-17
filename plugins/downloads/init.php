<?php
/**
 * Downloads Plugin Initializer
 */

// --- Autoloader ---
spl_autoload_register(function ($class) {
    $prefix = 'Plugins\\Downloads\\';
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
function downloads_register_routes() {
    $router = \App\Core\Router::getInstance();

    // Frontend routes
    $router->add('downloads', ['controller' => 'Plugins\\Downloads\\Controllers\\DownloadController', 'action' => 'index']);
    $router->add('downloads/category/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\DownloadController', 'action' => 'category']);
    $router->add('downloads/file/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\DownloadController', 'action' => 'file']);
    $router->add('downloads/go/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\DownloadController', 'action' => 'go']); // The actual download link
    $router->add('downloads/upload', ['controller' => 'Plugins\\Downloads\\Controllers\\DownloadController', 'action' => 'upload', 'method' => 'GET']);
    $router->add('downloads/save', ['controller' => 'Plugins\\Downloads\\Controllers\\DownloadController', 'action' => 'save', 'method' => 'POST']);

    // Admin routes
    $router->add('admin/downloads', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\DownloadController', 'action' => 'index']);
    // Categories
    $router->add('admin/downloads/categories', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\DownloadController', 'action' => 'categories']);
    $router->add('admin/downloads/categories/new', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\DownloadController', 'action' => 'newCategory']);
    $router->add('admin/downloads/categories/create', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\DownloadController', 'action' => 'createCategory', 'method' => 'POST']);
    $router->add('admin/downloads/categories/edit/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\DownloadController', 'action' => 'editCategory']);
    $router->add('admin/downloads/categories/update/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\DownloadController', 'action' => 'updateCategory', 'method' => 'POST']);
    $router->add('admin/downloads/categories/delete/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\DownloadController', 'action' => 'deleteCategory']);
    // Files
    $router->add('admin/downloads/files', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\DownloadController', 'action' => 'files']);
    $router->add('admin/downloads/files/validate/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\DownloadController', 'action' => 'validateFile']);
    $router->add('admin/downloads/files/delete/{id:\d+}', ['controller' => 'Plugins\\Downloads\\Controllers\\Admin\\DownloadController', 'action' => 'deleteFile']);
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

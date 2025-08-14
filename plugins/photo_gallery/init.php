<?php
/**
 * Photo Gallery Plugin Initializer
 */

// --- Autoloader ---
spl_autoload_register(function ($class) {
    $prefix = 'Plugins\\PhotoGallery\\';
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
function photo_gallery_register_routes() {
    $router = \App\Core\Router::getInstance();

    // Frontend routes
    $router->add('gallery', ['controller' => 'Plugins\\PhotoGallery\\Controllers\\GalleryController', 'action' => 'index']);
    $router->add('gallery/album/{id:\d+}', ['controller' => 'Plugins\\PhotoGallery\\Controllers\\GalleryController', 'action' => 'album']);
    $router->add('gallery/photo/{id:\d+}', ['controller' => 'Plugins\\PhotoGallery\\Controllers\\GalleryController', 'action' => 'photo']);
    // We'll need routes for users to manage their own albums and photos too.
    $router->add('my-gallery/upload', ['controller' => 'Plugins\\PhotoGallery\\Controllers\\UserGalleryController', 'action' => 'upload']);

    // Admin routes
    $router->add('admin/gallery', ['controller' => 'Plugins\\PhotoGallery\\Controllers\\Admin\\GalleryController', 'action' => 'index']);
}
\App\Core\Hooks::listen('register_routes', 'photo_gallery_register_routes');

// Register view paths
\App\Core\View::registerPluginViews('photo_gallery_frontend', __DIR__ . '/views/frontend');
\App\Core\View::registerPluginViews('photo_gallery_admin', __DIR__ . '/views/admin');


// --- Hook Registration ---
\App\Core\Hooks::listen('admin_sidebar_links', function() {
    // if (\App\Core\Auth::hasPermission('gallery.manage')) {
        echo '<a href="/admin/gallery">Photo Gallery</a>';
    // }
});

\App\Core\Hooks::listen('main_nav_links', function() {
    echo '<a href="/gallery">Gallery</a>';
});

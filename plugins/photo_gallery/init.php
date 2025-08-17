<?php
/**
 * Photo Gallery Plugin Initializer
 */

// Register this plugin's namespace with the main autoloader
\App\Core\Autoloader::addNamespace('Plugins\\PhotoGallery', __DIR__ . '/src');


// --- Route Registration ---
function photo_gallery_register_routes() {
    $router = \App\Core\Router::getInstance();

    // Frontend routes
    $router->add('gallery', ['controller' => 'Plugins\\PhotoGallery\\Controllers\\Gallery', 'action' => 'index']);
    $router->add('gallery/album/{id:\d+}', ['controller' => 'Plugins\\PhotoGallery\\Controllers\\Gallery', 'action' => 'album']);
    $router->add('gallery/photo/{id:\d+}', ['controller' => 'Plugins\\PhotoGallery\\Controllers\\Gallery', 'action' => 'photo']);
    // We'll need routes for users to manage their own albums and photos too.
    $router->add('my-gallery/upload', ['controller' => 'Plugins\\PhotoGallery\\Controllers\\UserGallery', 'action' => 'upload']);

    // Admin routes
    $router->add('admin/gallery', ['controller' => 'Plugins\\PhotoGallery\\Controllers\\Admin\\Gallery', 'action' => 'index']);
}
\App\Core\Hooks::listen('register_routes', 'photo_gallery_register_routes');

// Register view paths
\App\Core\View::registerPluginViews('photo_gallery_frontend', __DIR__ . '/views/frontend');
\App\Core\View::registerPluginViews('photo_gallery_admin', __DIR__ . '/views/admin');


// --- Hook Registration ---
\App\Core\Hooks::listen('admin_sidebar_links', function() {
    // if (\App\Core\Auth::hasPermission('gallery.manage')) {
        echo '<a href="' . url('admin/gallery') . '">Photo Gallery</a>';
    // }
});

\App\Core\Hooks::listen('main_nav_links', function() {
    echo '<a href="' . url('gallery') . '">Gallery</a>';
});

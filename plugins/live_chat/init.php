<?php
/**
 * Live Chat Plugin Initializer
 */

// Register this plugin's namespace with the main autoloader
\App\Core\Autoloader::addNamespace('Plugins\\LiveChat', __DIR__ . '/src');


// --- Route Registration ---
function live_chat_register_routes() {
    $router = \App\Core\Router::getInstance();

    // Frontend routes
    $router->add('chat', ['controller' => 'Plugins\\LiveChat\\Controllers\\Chat', 'action' => 'index']);

    // API routes for fetching messages, etc.
    $router->add('api/chat/messages/{room_id:\d+}', ['controller' => 'Plugins\\LiveChat\\Controllers\\Api', 'action' => 'getMessages']);
    $router->add('api/chat/send', ['controller' => 'Plugins\\LiveChat\\Controllers\\Api', 'action' => 'sendMessage', 'method' => 'POST']);

    // Admin routes
    $router->add('admin/chat', ['controller' => 'Plugins\\LiveChat\\Controllers\\Admin\\Chat', 'action' => 'index']);
}
\App\Core\Hooks::listen('register_routes', 'live_chat_register_routes');


// --- Hook Registration ---
// Add a link to the admin sidebar
\App\Core\Hooks::listen('admin_sidebar_links', function() {
    // We need a specific permission for chat
    // if (\App\Core\Auth::hasPermission('chat.manage')) {
        echo '<a href="' . url('admin/chat') . '">Manage Chat</a>';
    // }
});

// Add a link to the main navigation
\App\Core\Hooks::listen('main_nav_links', function() {
    echo '<a href="' . url('chat') . '">Chat</a>';
});

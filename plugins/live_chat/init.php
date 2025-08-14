<?php
/**
 * Live Chat Plugin Initializer
 */

// --- Autoloader for Live Chat Plugin Classes ---
spl_autoload_register(function ($class) {
    $prefix = 'Plugins\\LiveChat\\';
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
function live_chat_register_routes() {
    $router = \App\Core\Router::getInstance();

    // Frontend routes
    $router->add('chat', ['controller' => 'Plugins\\LiveChat\\Controllers\\ChatController', 'action' => 'index']);

    // API routes for fetching messages, etc.
    $router->add('api/chat/messages/{room_id:\d+}', ['controller' => 'Plugins\\LiveChat\\Controllers\\ApiController', 'action' => 'getMessages']);
    $router->add('api/chat/send', ['controller' => 'Plugins\\LiveChat\\Controllers\\ApiController', 'action' => 'sendMessage', 'method' => 'POST']);

    // Admin routes
    $router->add('admin/chat', ['controller' => 'Plugins\\LiveChat\\Controllers\\Admin\\ChatController', 'action' => 'index']);
}
\App\Core\Hooks::listen('register_routes', 'live_chat_register_routes');


// --- Hook Registration ---
// Add a link to the admin sidebar
\App\Core\Hooks::listen('admin_sidebar_links', function() {
    // We need a specific permission for chat
    // if (\App\Core\Auth::hasPermission('chat.manage')) {
        echo '<a href="/admin/chat">Manage Chat</a>';
    // }
});

// Add a link to the main navigation
\App\Core\Hooks::listen('main_nav_links', function() {
    echo '<a href="/chat">Chat</a>';
});

<?php
/**
 * Support Tickets Plugin Initializer
 */

// --- Autoloader ---
spl_autoload_register(function ($class) {
    $prefix = 'Plugins\\SupportTickets\\';
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
function support_tickets_register_routes() {
    $router = \App\Core\Router::getInstance();

    // Frontend routes
    $router->add('tickets', ['controller' => 'Plugins\\SupportTickets\\Controllers\\TicketController', 'action' => 'index']);
    $router->add('tickets/new', ['controller' => 'Plugins\\SupportTickets\\Controllers\\TicketController', 'action' => 'new']);
    $router->add('tickets/create', ['controller' => 'Plugins\\SupportTickets\\Controllers\\TicketController', 'action' => 'create', 'method' => 'POST']);
    $router->add('tickets/{id:\d+}', ['controller' => 'Plugins\\SupportTickets\\Controllers\\TicketController', 'action' => 'show']);
    $router->add('tickets/{id:\d+}/reply', ['controller' => 'Plugins\\SupportTickets\\Controllers\\TicketController', 'action' => 'reply', 'method' => 'POST']);

    // Admin routes
    $router->add('admin/tickets', ['controller' => 'Plugins\\SupportTickets\\Controllers\\Admin\\TicketController', 'action' => 'index']);
    $router->add('admin/tickets/{id:\d+}', ['controller' => 'Plugins\\SupportTickets\\Controllers\\Admin\\TicketController', 'action' => 'show']);
    $router->add('admin/tickets/{id:\d+}/update', ['controller' => 'Plugins\\SupportTickets\\Controllers\\Admin\\TicketController', 'action' => 'update', 'method' => 'POST']);
}
\App\Core\Hooks::listen('register_routes', 'support_tickets_register_routes');


// --- Hook Registration ---
\App\Core\Hooks::listen('admin_sidebar_links', function() {
    // if (\App\Core\Auth::hasPermission('tickets.manage')) {
        echo '<a href="/admin/tickets">Support Tickets</a>';
    // }
});

\App\Core\Hooks::listen('main_nav_links', function() {
    if (\App\Core\Auth::check()) {
        echo '<a href="/tickets">My Tickets</a>';
    }
});

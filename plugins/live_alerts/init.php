<?php
/**
 * Live Alerts Plugin Initializer
 */

// --- Autoloader ---
spl_autoload_register(function ($class) {
    $prefix = 'Plugins\\LiveAlerts\\';
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
function live_alerts_register_routes() {
    $router = \App\Core\Router::getInstance();

    // API routes for checking and marking alerts as read
    $router->add('api/alerts/check', ['controller' => 'Plugins\\LiveAlerts\\Controllers\\ApiController', 'action' => 'check']);
    $router->add('api/alerts/mark_read/{id:\d+}', ['controller' => 'Plugins\\LiveAlerts\\Controllers\\ApiController', 'action' => 'markAsRead', 'method' => 'POST']);

    // Admin routes
    $router->add('admin/alerts/send', ['controller' => 'Plugins\\LiveAlerts\\Controllers\\Admin\\AlertController', 'action' => 'send', 'method' => 'POST']);
}
\App\Core\Hooks::listen('register_routes', 'live_alerts_register_routes');


// --- Hook Registration ---
// Add the alert container and JS to the site footer
\App\Core\Hooks::listen('site_footer', function() {
    if (\App\Core\Auth::check()) {
        $check_url = url('api/alerts/check');
        $mark_read_url_base = url('api/alerts/mark_read'); // JS will add the ID
        echo '<div id="live-alert-overlay" style="display: none;" data-check-url="' . $check_url . '" data-mark-read-url-base="' . $mark_read_url_base . '">';
        echo '  <div id="live-alert-box">';
        echo '      <h2 id="live-alert-title">Alert</h2>';
        echo '      <div id="live-alert-content"></div>';
        echo '      <button id="live-alert-close">Close</button>';
        echo '  </div>';
        echo '</div>';
        echo '<script src="/public/plugins/live_alerts/assets/js/alerts.js"></script>';
    }
});

// Add a "Send Alert" button to the user management list
\App\Core\Hooks::listen('admin_user_actions', function($user) {
    // A new hook we will need to create in the user list view
    echo '<a href="#" class="btn-action send-alert" data-user-id="' . $user['id'] . '" data-send-url="' . url('admin/alerts/send') . '">Send Alert</a>';
});

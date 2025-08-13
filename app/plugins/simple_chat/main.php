<?php
/**
 * Plugin Name: Simple Ajax Chat
 * Plugin URI: -
 * Description: A simple chat plugin.
 * Version: 1.0.0
 * Author: Jules
 */

// Prevent direct access
if (!defined('APP_LOADED') && !defined('ADMIN_AREA')) {
    die('Forbidden');
}

// Here we will register our plugin's routes, hooks, and functions.

// Example of how we might add a route later:
/*
function simple_chat_register_routes($router) {
    $router->add('chat', function() {
        include __DIR__ . '/pages/chat_index.php';
    });
    $router->add('chat/room/(\d+)', function($room_id) {
        $_GET['room_id'] = $room_id;
        include __DIR__ . '/pages/chat_room.php';
    });
}
// This would require a global router object or a hook system, which we don't have yet.
// For now, we can add the route directly to the main index.php when the plugin is active,
// or create a simpler routing mechanism.
*/

// For now, let's just define a function that the main app can see.
function simple_chat_is_active() {
    return true;
}
?>

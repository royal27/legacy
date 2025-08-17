<?php
/**
 * Hello World Plugin
 *
 * This is an example of a simple plugin.
 */

// Define a function for our plugin to use.
function hello_world_add_admin_link() {
    echo '<a href="#" class="btn-action">Hello World!</a>';
}

// Add the function to the hook.
\App\Core\Hooks::listen('admin_dashboard_nav_links', 'hello_world_add_admin_link');

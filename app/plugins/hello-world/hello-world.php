<?php
/**
 * Plugin Name: Hello World
 * Version: 1.0
 * Description: A simple plugin to demonstrate the hooks system.
 * Author: Jules
 */

// Prevent direct access
if (!defined('DB_HOST')) { // A simple check, assuming DB_HOST is always defined by the app
    die('Cannot access directly.');
}

// 1. Add a function to an action hook
function hw_add_footer_message() {
    echo '<p style="text-align:center; color: #888; font-size: 0.9em;">Hello World! This message is from a plugin.</p>';
}
Hooks::add_action('footer_content', 'hw_add_footer_message');


// 2. Add a function to a filter hook
function hw_filter_title($title) {
    return $title . ' - Modified by a Plugin!';
}
Hooks::add_filter('page_title', 'hw_filter_title');

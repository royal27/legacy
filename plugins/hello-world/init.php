<?php
// Hello World Plugin: Main File

/**
 * This function displays the "Hello World" message.
 */
function hello_world_message() {
    echo '<div style="background-color: #ffeb3b; text-align: center; padding: 10px; font-weight: bold;">';
    echo 'Hello World! This message is from an active plugin.';
    echo '</div>';
}

/**
 * Register the function to the 'after_header_nav' hook.
 */
add_action('after_header_nav', 'hello_world_message');
?>

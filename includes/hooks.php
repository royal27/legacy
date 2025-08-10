<?php
// A simple action and hook system.
$hooks = [];

/**
 * Registers a function to a specific action hook.
 */
function add_action($hook_name, $function_to_call, $priority = 10) {
    global $hooks;
    $hooks[$hook_name][$priority][] = $function_to_call;
}

/**
 * Executes all functions registered to a specific action hook.
 */
function do_action($hook_name, ...$args) {
    global $hooks;

    if (isset($hooks[$hook_name])) {
        ksort($hooks[$hook_name]);

        foreach ($hooks[$hook_name] as $priority_group) {
            foreach ($priority_group as $function_to_call) {
                if (is_callable($function_to_call)) {
                    call_user_func_array($function_to_call, $args);
                }
            }
        }
    }
}
?>

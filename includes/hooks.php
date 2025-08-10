<?php
// A simple action and hook system.

// Global array to store all registered actions
$hooks = [];

/**
 * Registers a function to a specific action hook.
 *
 * @param string $hook_name The name of the hook.
 * @param callable $function_to_call The function to be executed.
 * @param int $priority The execution priority (lower numbers run first).
 */
function add_action($hook_name, $function_to_call, $priority = 10) {
    global $hooks;

    if (!isset($hooks[$hook_name])) {
        $hooks[$hook_name] = [];
    }
    if (!isset($hooks[$hook_name][$priority])) {
        $hooks[$hook_name][$priority] = [];
    }

    $hooks[$hook_name][$priority][] = $function_to_call;
}

/**
 * Executes all functions registered to a specific action hook.
 *
 * @param string $hook_name The name of the hook to execute.
 * @param mixed $args Optional arguments to pass to the hooked functions.
 */
function do_action($hook_name, ...$args) {
    global $hooks;

    if (isset($hooks[$hook_name])) {
        ksort($hooks[$hook_name]); // Sort by priority

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

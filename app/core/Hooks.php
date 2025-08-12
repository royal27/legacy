<?php

/**
 * Hooks Class
 * A simple action and filter system for plugins.
 */
class Hooks {
    private static $actions = [];
    private static $filters = [];

    /**
     * Add a function to a specific action hook.
     * @param string $tag The name of the action.
     * @param callable $callback The function to be called.
     * @param int $priority The order in which the functions are executed.
     */
    public static function add_action($tag, $callback, $priority = 10) {
        if (!isset(self::$actions[$tag])) {
            self::$actions[$tag] = [];
        }
        self::$actions[$tag][$priority][] = $callback;
    }

    /**
     * Execute all functions hooked to a specific action.
     * @param string $tag The name of the action to be executed.
     * @param mixed ...$args Optional arguments to pass to the callback functions.
     */
    public static function do_action($tag, ...$args) {
        if (isset(self::$actions[$tag])) {
            ksort(self::$actions[$tag]); // Sort by priority
            foreach (self::$actions[$tag] as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    call_user_func_array($callback, $args);
                }
            }
        }
    }

    /**
     * Add a function to a specific filter hook.
     * @param string $tag The name of the filter.
     * @param callable $callback The function to be called.
     * @param int $priority The order in which the functions are executed.
     */
    public static function add_filter($tag, $callback, $priority = 10) {
        if (!isset(self::$filters[$tag])) {
            self::$filters[$tag] = [];
        }
        self::$filters[$tag][$priority][] = $callback;
    }

    /**
     * Apply all functions hooked to a specific filter.
     * @param string $tag The name of the filter to apply.
     * @param mixed $value The value to be filtered.
     * @return mixed The filtered value.
     */
    public static function apply_filters($tag, $value) {
        if (isset(self::$filters[$tag])) {
            ksort(self::$filters[$tag]); // Sort by priority
            $args = func_get_args();
            array_shift($args); // Remove the tag from the arguments list

            foreach (self::$filters[$tag] as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    // We need to pass the value as the first argument
                    $args[0] = $value;
                    $value = call_user_func_array($callback, $args);
                }
            }
        }
        return $value;
    }
}

<?php

namespace App\Core;

/**
 * A simple hook and filter system (event system).
 */
class Hooks
{
    /**
     * @var array Stores all the registered listeners.
     */
    protected static $listeners = [];

    /**
     * Register a listener for a given event.
     *
     * @param string $event The name of the event.
     * @param callable $callback The function to execute.
     * @param int $priority The priority of execution.
     */
    public static function listen($event, $callback, $priority = 10)
    {
        self::$listeners[$event][$priority][] = $callback;
    }

    /**
     * Trigger an action event.
     *
     * This executes all listeners for an event but does not return any value.
     *
     * @param string $event The name of the event.
     * @param mixed ...$args Arguments to pass to the listener functions.
     */
    public static function trigger($event, ...$args)
    {
        if (isset(self::$listeners[$event])) {
            ksort(self::$listeners[$event]); // Sort by priority
            foreach (self::$listeners[$event] as $priority_group) {
                foreach ($priority_group as $callback) {
                    call_user_func_array($callback, $args);
                }
            }
        }
    }

    /**
     * Apply filters to a value.
     *
     * This executes all listeners for an event, passing a value through each one
     * and returning the final modified value.
     *
     * @param string $event The name of the filter event.
     * @param mixed $value The initial value to be filtered.
     * @param mixed ...$args Additional arguments to pass to the filter functions.
     * @return mixed The filtered value.
     */
    public static function filter($event, $value, ...$args)
    {
        if (isset(self::$listeners[$event])) {
            ksort(self::$listeners[$event]); // Sort by priority
            foreach (self::$listeners[$event] as $priority_group) {
                foreach ($priority_group as $callback) {
                    // Prepend the value to the arguments array for the callback
                    $value = call_user_func_array($callback, array_merge([$value], $args));
                }
            }
        }
        return $value;
    }
}

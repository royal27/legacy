<?php
// src/includes/plugin_handler.php

/**
 * A very simple hook system for our plugin architecture.
 */
class PluginHandler {
    private static $instance;
    private $hooks = [];

    private function __construct() {}

    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Registers a function to a specific hook.
     *
     * @param string $hook The name of the hook (e.g., 'frontend_content').
     * @param callable $function The function to execute.
     */
    public function register_hook(string $hook, callable $function) {
        $this->hooks[$hook][] = $function;
    }

    /**
     * Executes all functions registered to a hook.
     *
     * @param string $hook The name of the hook to execute.
     * @param mixed ...$args Arguments to pass to the hooked functions.
     */
    public function do_hook(string $hook, ...$args) {
        if (isset($this->hooks[$hook])) {
            foreach ($this->hooks[$hook] as $function) {
                call_user_func_array($function, $args);
            }
        }
    }
}

/**
 * Helper function to register a hook.
 *
 * @param string $hook
 * @param callable $function
 */
function register_hook(string $hook, callable $function) {
    PluginHandler::get_instance()->register_hook($hook, $function);
}

/**
 * Helper function to execute a hook.
 *
 * @param string $hook
 * @param mixed ...$args
 */
function do_hook(string $hook, ...$args) {
    PluginHandler::get_instance()->do_hook($hook, ...$args);
}

/**
 * Loads all active plugins from the database.
 * This function should be called once per page load.
 */
function load_active_plugins() {
    $conn = db_connect();
    $active_plugins = $conn->query("SELECT plugin_folder FROM plugins WHERE is_active = 1");

    if ($active_plugins) {
        while ($plugin = $active_plugins->fetch_assoc()) {
            $plugin_init_file = __DIR__ . '/../../plugins/' . $plugin['plugin_folder'] . '/init.php';
            if (file_exists($plugin_init_file)) {
                // By including the file, it can call register_hook()
                include_once $plugin_init_file;
            }
        }
    }
    $conn->close();
}
?>

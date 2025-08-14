<?php

namespace App\Core;

class View
{
    /**
     * @var array Paths to plugin view directories.
     */
    protected static $plugin_view_paths = [];

    /**
     * Register a views directory for a plugin.
     *
     * @param string $namespace The plugin's namespace (e.g., 'forum')
     * @param string $path The absolute path to the plugin's views directory
     */
    public static function registerPluginViews($namespace, $path)
    {
        self::$plugin_view_paths[$namespace] = $path;
    }

    /**
     * Render a view file
     *
     * @param string $view The view file to render (e.g., 'Home/index.php' or '@forum/index.php')
     * @param array $args Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = self::resolveViewPath($view);

        if (is_readable($file)) {
            ob_start();
            require $file;
            $content = ob_get_clean();
            require dirname(__DIR__, 2) . '/templates/default/layout.php';
        } else {
            throw new \Exception("View file '$file' not found for view '$view'");
        }
    }

    /**
     * Resolve the full path to a view file, handling plugin namespaces.
     *
     * @param string $view
     * @return string
     */
    protected static function resolveViewPath($view)
    {
        if (strpos($view, '@') === 0) {
            // It's a plugin view
            list($namespace, $path) = explode('/', substr($view, 1), 2);
            if (isset(self::$plugin_view_paths[$namespace])) {
                return self::$plugin_view_paths[$namespace] . '/' . $path;
            }
        }

        // It's a core view
        return dirname(__DIR__, 2) . "/templates/default/views/$view";
    }
}

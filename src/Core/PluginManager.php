<?php

namespace App\Core;

class PluginManager
{
    protected $plugins_dir;
    protected $all_plugins = [];

    public function __construct()
    {
        $this->plugins_dir = dirname(__DIR__, 2) . '/plugins';
        $this->scanPlugins();
    }

    /**
     * Scan the plugins directory and load their manifest files.
     */
    public function scanPlugins()
    {
        $plugin_dirs = array_filter(scandir($this->plugins_dir), function ($item) {
            return !in_array($item, ['.', '..']) && is_dir($this->plugins_dir . '/' . $item);
        });

        foreach ($plugin_dirs as $dir) {
            $manifest_path = $this->plugins_dir . '/' . $dir . '/plugin.json';
            if (file_exists($manifest_path)) {
                $manifest = json_decode(file_get_contents($manifest_path), true);
                if ($manifest) {
                    $this->all_plugins[$dir] = $manifest;
                }
            }
        }
    }

    /**
     * Get all found plugins.
     *
     * @return array
     */
    public function getAllPlugins()
    {
        return $this->all_plugins;
    }

    /**
     * Load the init.php file for all active plugins.
     */
    public function loadActivePlugins()
    {
        $active_plugins = \App\Models\Plugin::getActivePlugins();

        foreach ($active_plugins as $plugin_dir) {
            $init_file = $this->plugins_dir . '/' . $plugin_dir . '/init.php';
            if (file_exists($init_file)) {
                include_once $init_file;
            }
        }
    }
}

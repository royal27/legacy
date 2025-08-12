<?php

class PluginManager {

    /**
     * Load all active plugins.
     * This method should be called once on application startup.
     */
    public static function load_active_plugins() {
        // This requires a database connection, so we assume it's available.
        $db = new Database();
        $prefix = $db->getPrefix();

        $db->query("SELECT folder_name FROM {$prefix}plugins WHERE is_active = 1");
        $active_plugins = $db->resultSet();

        if ($active_plugins) {
            foreach ($active_plugins as $plugin) {
                $plugin_file = '../app/plugins/' . $plugin['folder_name'] . '/' . $plugin['folder_name'] . '.php';
                if (file_exists($plugin_file)) {
                    require_once $plugin_file;
                }
            }
        }
    }

    /**
     * Scans the plugins directory and returns an array of plugin data.
     * @return array
     */
    public static function get_all_plugins() {
        $plugins = [];
        $plugins_dir = '../app/plugins/';
        $plugin_folders = array_filter(scandir($plugins_dir), function ($item) use ($plugins_dir) {
            return is_dir($plugins_dir . $item) && !in_array($item, ['.', '..']);
        });

        foreach ($plugin_folders as $folder) {
            $plugin_file = $plugins_dir . $folder . '/' . $folder . '.php';
            if (file_exists($plugin_file)) {
                // We don't need to parse the file here, just get the header data
                $plugin_data = self::get_plugin_data($plugin_file);
                if (!empty($plugin_data['Plugin Name'])) {
                    $plugins[$folder] = $plugin_data;
                }
            }
        }
        return $plugins;
    }

    /**
     * Parses the plugin file header to get metadata.
     * @param string $plugin_file Path to the main plugin file.
     * @return array
     */
    public static function get_plugin_data($plugin_file) {
        $default_headers = [
            'Plugin Name' => 'Plugin Name',
            'Version' => 'Version',
            'Description' => 'Description',
            'Author' => 'Author'
        ];

        $fp = fopen($plugin_file, 'r');
        $file_data = fread($fp, 8192); // Pull only the first 8kiB of the file
        fclose($fp);

        $file_data = str_replace("\r", "\n", $file_data);
        $all_headers = $default_headers;

        foreach ($all_headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $file_data, $match) && $match[1]) {
                $all_headers[$field] = trim(preg_replace('/\s*(?:\*\/|\?>).*/', '', $match[1]));
            } else {
                $all_headers[$field] = '';
            }
        }

        return $all_headers;
    }

    // Activate, Deactivate, and other management functions will be added here later.
    // These will be called from the Admin Panel.
}

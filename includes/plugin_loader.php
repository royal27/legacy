<?php
// This file loads all active plugins.

if (!isset($mysqli)) {
    die("Database connection is not available for plugin loader.");
}

$prefix = DB_PREFIX;
$sql = "SELECT `directory_name` FROM `{$prefix}plugins` WHERE `is_active` = 1";
$result = $mysqli->query($sql);

if ($result) {
    while ($plugin = $result->fetch_assoc()) {
        $plugin_dir = $plugin['directory_name'];
        $plugins_base_path = __DIR__ . '/../plugins/';

        $manifest_file = $plugins_base_path . $plugin_dir . '/plugin.json';
        if (file_exists($manifest_file)) {
            $manifest = json_decode(file_get_contents($manifest_file), true);
            if (isset($manifest['main_file'])) {
                $main_plugin_file = $plugins_base_path . $plugin_dir . '/' . $manifest['main_file'];
                if (file_exists($main_plugin_file)) {
                    require_once $main_plugin_file;
                }
            }
        }
    }
}
?>

<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\PluginManager;
use App\Core\Session;
use App\Core\View;
use App\Models\Plugin;

class PluginsController extends Controller
{
    public function __construct($route_params)
    {
        parent::__construct($route_params);
        // We might want a specific 'plugins.manage' permission later
        if (!Auth::hasPermission('settings.edit')) {
            Session::flash('error', 'You do not have permission to manage plugins.');
            header('Location: ' . url('admin'));
            exit;
        }
    }

    /**
     * Show the plugin management page.
     */
    public function index()
    {
        $pluginManager = new PluginManager();
        $all_plugins = $pluginManager->getAllPlugins();
        $active_plugins = Plugin::getActivePlugins();

        View::render('Admin/Plugins/index.php', [
            'title' => 'Plugin Management',
            'all_plugins' => $all_plugins,
            'active_plugins' => $active_plugins
        ]);
    }

    /**
     * Activate a plugin.
     */
    public function activate()
    {
        $plugin_dir = $this->route_params['plugin_dir'] ?? null;
        if ($plugin_dir && Plugin::activate($plugin_dir)) {
            Session::flash('success', "Plugin '$plugin_dir' activated.");
        } else {
            Session::flash('error', "Failed to activate plugin '$plugin_dir'.");
        }
        header('Location: ' . url('admin/plugins'));
        exit;
    }

    /**
     * Deactivate a plugin.
     */
    public function deactivate()
    {
        $plugin_dir = $this->route_params['plugin_dir'] ?? null;
        if ($plugin_dir && Plugin::deactivate($plugin_dir)) {
            Session::flash('success', "Plugin '$plugin_dir' deactivated.");
        } else {
            Session::flash('error', "Failed to deactivate plugin '$plugin_dir'.");
        }
        header('Location: ' . url('admin/plugins'));
        exit;
    }
}

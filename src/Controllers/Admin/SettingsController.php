<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Core\Auth;
use App\Core\Session;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function __construct($route_params)
    {
        parent::__construct($route_params);
        if (!Auth::hasPermission('settings.edit')) {
            Session::flash('error', 'You do not have permission to manage settings.');
            header('Location: /admin');
            exit;
        }
    }

    /**
     * Show the settings page.
     */
    public function index()
    {
        $settings = Setting::getAll();
        View::render('Admin/Settings/index.php', [
            'title' => 'Site Settings',
            'settings' => $settings
        ]);
    }

    /**
     * Update settings.
     */
    public function update()
    {
        // For now, we just grab all POST data.
        // A more robust solution would validate and sanitize this.
        Setting::updateBatch($_POST);
        Session::flash('success', 'Settings updated successfully.');
        header('Location: /admin/settings');
        exit;
    }
}

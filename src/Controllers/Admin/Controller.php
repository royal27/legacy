<?php

namespace App\Controllers\Admin;

use App\Core\Session;

/**
 * Base controller for the admin area
 *
 * All controllers in the Admin namespace should extend this class.
 */
abstract class Controller extends \App\Core\Controller
{
    /**
     * Class constructor
     *
     * This method is executed before any action method in the child controller.
     * It checks if the user is logged in.
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        parent::__construct($route_params);

        if (!Session::has('user_id')) {
            Session::flash('error', 'You must be logged in to view that page.');
            header('Location: /login');
            exit;
        }

        // We can add role-based access control here later
        // For example:
        // $role_id = Session::get('role_id');
        // if ($role_id != 1) { // Assuming 1 is the Founder/Admin role
        //    die('Permission denied.');
        // }
    }
}

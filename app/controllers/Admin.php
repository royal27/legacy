<?php

class Admin extends Controller {
    public function __construct() {
        // Protect the entire admin area
        if (!Auth::isLoggedIn()) {
            Session::flash('error', 'You must be logged in to view that page.');
            header('Location: /users/login');
            exit;
        }
        // Check for a general admin permission.
        // The Founder role will always pass this check.
        if (!Auth::check('admin.access')) {
            Session::flash('error', 'You do not have permission to access the admin panel.');
            header('Location: /dashboard');
            exit;
        }
    }

    public function index() {
        $data = [
            'title' => 'Admin Dashboard'
        ];

        // For now, we will load a simple view.
        // Later, we might have a separate admin template.
        $this->view('admin/index', $data);
    }

    // Example of a protected admin page
    public function users() {
        if (!Auth::check('admin.users.manage')) {
             Session::flash('error', 'You do not have permission to manage users.');
             header('Location: /admin');
             exit;
        }

        $data = ['title' => 'Manage Users'];
        $this->view('admin/users', $data);
    }
}

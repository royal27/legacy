<?php

class Dashboard extends Controller {
    public function __construct() {
        // This entire controller should be protected.
        // If the user is not logged in, redirect them to the login page.
        if (!Auth::isLoggedIn()) {
            Session::flash('error', 'You must be logged in to view that page.');
            header('Location: /users/login');
            exit;
        }
    }

    public function index() {
        $data = [
            'title' => 'User Dashboard',
            'user' => Auth::user()
        ];

        $this->view('dashboard/index', $data);
    }
}

<?php

namespace App\Controllers\Admin;

use App\Core\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard
     *
     * @return void
     */
    public function index()
    {
        View::render('Admin/Dashboard/index.php', [
            'title' => 'Admin Dashboard'
        ]);
    }
}

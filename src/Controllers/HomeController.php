<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;

class HomeController extends Controller
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function index()
    {
        View::render('Home/index.php', [
            'name' => 'Guest',
            'title' => 'Homepage'
        ]);
    }
}

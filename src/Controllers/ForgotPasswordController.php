<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password page
     */
    public function index()
    {
        View::render('Auth/forgot-password.php', [
            'title' => 'Forgot Password'
        ]);
    }

    /**
     * Handle the form submission
     */
    public function sendResetLink()
    {
        // Logic to handle password reset email will go here.
        // For now, just show a success message.
        \App\Core\Session::flash('success', 'If an account with that email exists, a reset link has been sent.');
        header('Location: ' . url('forgot-password'));
        exit;
    }
}

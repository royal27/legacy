<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Core\Session;
use App\Core\Database;
use App\Core\Auth;

class AuthController extends Controller
{
    /**
     * Show the login page
     *
     * @return void
     */
    public function login()
    {
        View::render('Auth/login.php', [
            'title' => 'Login'
        ]);
    }

    /**
     * Process the login form submission
     *
     * @return void
     */
    public function postLogin()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            Session::flash('error', 'Username and password are required.');
            header('Location: ' . url('login'));
            exit;
        }

        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;

        $stmt = $db->prepare("SELECT id, username, email, password, role_id FROM {$prefix}users WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, user is authenticated
            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            Session::set('role_id', $user['role_id']);

            // Load permissions into session
            Auth::loadPermissions($user['role_id']);

            // Redirect to a protected page, e.g., admin dashboard
            header('Location: ' . url('admin'));
            exit;
        } else {
            // Invalid credentials
            Session::flash('error', 'Invalid username or password.');
            header('Location: ' . url('login'));
            exit;
        }
    }

    /**
     * Log the user out
     *
     * @return void
     */
    public function logout()
    {
        Session::destroy();
        header('Location: ' . url(''));
        exit;
    }
}

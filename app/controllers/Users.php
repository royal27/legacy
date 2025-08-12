<?php

class Users extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function login() {
        // Redirect if already logged in
        if (Auth::isLoggedIn()) {
            header('Location: /');
            exit;
        }

        $data = [
            'title' => 'Login',
            'email' => '',
            'password' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);

            // Validation
            if (empty($data['email']) || empty($data['password'])) {
                $data['error'] = 'Please fill out all fields.';
            }

            // Check for user
            if (empty($data['error'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    // Create session
                    $_SESSION['user_id'] = $loggedInUser['id'];
                    header('Location: /'); // Redirect to homepage
                    exit;
                } else {
                    $data['error'] = 'Password or email is incorrect.';
                }
            }
        }

        $this->view('users/login', $data);
    }

    public function register() {
        // Redirect if already logged in
        if (Auth::isLoggedIn()) {
            header('Location: /');
            exit;
        }

        $data = [
            'title' => 'Register',
            'username' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'errors' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            // ... (validation logic would go here)

            // For now, a simple registration
            if ($this->userModel->register($_POST)) {
                Session::flash('success', 'You have registered successfully! Please log in.');
                header('Location: /users/login');
                exit;
            } else {
                // Handle registration failure
                Session::flash('error', 'Something went wrong. Could not register user.');
                header('Location: /users/register');
                exit;
            }
        }

        $this->view('users/register', $data);
    }

    public function logout() {
        Auth::logout();
        header('Location: /users/login');
        exit;
    }
}

<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\User;
use App\Models\Role;
use App\Core\Auth;
use App\Core\Session;

class UsersController extends Controller
{
    public function __construct($route_params)
    {
        parent::__construct($route_params);
        if (!Auth::hasPermission('users.view')) {
            Session::flash('error', 'You do not have permission to manage users.');
            header('Location: ' . url('admin'));
            exit;
        }
    }

    /**
     * Show the user management page (list of users)
     *
     * @return void
     */
    public function index()
    {
        $users = User::getAll();

        View::render('Admin/Users/index.php', [
            'title' => 'User Management',
            'users' => $users
        ]);
    }

    /**
     * Show the form for editing a user.
     *
     * @return void
     */
    public function edit()
    {
        if (!Auth::hasPermission('users.edit')) {
            $this->forbidden();
        }

        $user_id = $this->route_params['id'];
        $user = User::findById($user_id);
        $roles = Role::getAll();

        if (!$user) {
            die('User not found.');
        }

        View::render('Admin/Users/edit.php', [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Update a user's information.
     *
     * @return void
     */
    public function update()
    {
        if (!Auth::hasPermission('users.edit')) {
            $this->forbidden();
        }

        $user_id = $this->route_params['id'];

        $data = [
            'username' => $_POST['username'] ?? null,
            'email' => $_POST['email'] ?? null,
            'role_id' => $_POST['role_id'] ?? null,
            'password' => $_POST['password'] ?? null
        ];

        if (empty($data['username']) || empty($data['email']) || empty($data['role_id'])) {
            Session::flash('error', 'Username, email, and role are required.');
            header("Location: " . url("admin/users/edit/$user_id"));
            exit;
        }

        if (User::update($user_id, $data)) {
            Session::flash('success', 'User updated successfully.');
        } else {
            Session::flash('error', 'Failed to update user.');
        }

        header('Location: ' . url('admin/users'));
        exit;
    }

    /**
     * Delete a user.
     *
     * @return void
     */
    public function delete()
    {
        if (!Auth::hasPermission('users.delete')) {
            $this->forbidden();
        }

        $user_id = $this->route_params['id'];

        if ($user_id == Session::get('user_id')) {
            Session::flash('error', 'You cannot delete your own account.');
            header('Location: ' . url('admin/users'));
            exit;
        }

        if (User::delete($user_id)) {
            Session::flash('success', 'User deleted successfully.');
        } else {
            Session::flash('error', 'Failed to delete user.');
        }

        header('Location: ' . url('admin/users'));
        exit;
    }

    /**
     * Show the form for creating a new user.
     *
     * @return void
     */
    public function new()
    {
        if (!Auth::hasPermission('users.create')) {
            $this->forbidden();
        }

        $roles = Role::getAll();
        View::render('Admin/Users/new.php', [
            'title' => 'Add New User',
            'roles' => $roles
        ]);
    }

    /**
     * Create a new user.
     *
     * @return void
     */
    public function create()
    {
        if (!Auth::hasPermission('users.create')) {
            $this->forbidden();
        }

        $data = [
            'username' => $_POST['username'] ?? null,
            'email' => $_POST['email'] ?? null,
            'password' => $_POST['password'] ?? null,
            'role_id' => $_POST['role_id'] ?? null
        ];

        if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['role_id'])) {
            Session::flash('error', 'All fields are required.');
            header("Location: " . url('admin/users/new'));
            exit;
        }

        if (User::create($data)) {
            Session::flash('success', 'User created successfully.');
        } else {
            Session::flash('error', 'Failed to create user. The username or email may already be taken.');
        }

        header('Location: ' . url('admin/users'));
        exit;
    }

    /**
     * Helper to show a forbidden error.
     */
    private function forbidden()
    {
        Session::flash('error', 'You do not have permission to perform this action.');
        header('Location: ' . url('admin/users'));
        exit;
    }
}

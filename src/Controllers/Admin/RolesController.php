<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Role;
use App\Models\Permission;
use App\Core\Auth;
use App\Core\Session;

class RolesController extends Controller
{
    public function __construct($route_params)
    {
        parent::__construct($route_params);
        if (!Auth::hasPermission('roles.view')) {
            Session::flash('error', 'You do not have permission to manage roles.');
            header('Location: /admin');
            exit;
        }
    }

    /**
     * Show the roles management page (list of roles)
     */
    public function index()
    {
        $roles = Role::getAll();

        View::render('Admin/Roles/index.php', [
            'title' => 'Role Management',
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function new()
    {
        if (!Auth::hasPermission('roles.create')) {
            $this->forbidden();
        }
        View::render('Admin/Roles/new.php', [
            'title' => 'Add New Role'
        ]);
    }

    /**
     * Create a new role.
     */
    public function create()
    {
        if (!Auth::hasPermission('roles.create')) {
            $this->forbidden();
        }
        $name = $_POST['name'] ?? '';

        if (empty($name)) {
            Session::flash('error', 'Role name is required.');
            header('Location: /admin/roles/new');
            exit;
        }

        if (Role::create($name)) {
            Session::flash('success', 'Role created successfully.');
        } else {
            Session::flash('error', 'Failed to create role.');
        }

        header('Location: /admin/roles');
        exit;
    }

    /**
     * Show the form for editing a role's permissions.
     */
    public function edit()
    {
        if (!Auth::hasPermission('roles.edit')) {
            $this->forbidden();
        }
        $role_id = $this->route_params['id'];
        $role = Role::findById($role_id);

        if (!$role) {
            die('Role not found.');
        }

        $all_permissions = Permission::getAll();
        $role_permissions = Role::getPermissions($role_id);

        View::render('Admin/Roles/edit.php', [
            'title' => 'Edit Role',
            'role' => $role,
            'all_permissions' => $all_permissions,
            'role_permissions' => $role_permissions
        ]);
    }

    /**
     * Update a role's permissions.
     */
    public function update()
    {
        if (!Auth::hasPermission('roles.edit')) {
            $this->forbidden();
        }
        $role_id = $this->route_params['id'];
        $permission_ids = $_POST['permissions'] ?? [];

        if (Role::updatePermissions($role_id, $permission_ids)) {
            Session::flash('success', 'Role permissions updated successfully.');
        } else {
            Session::flash('error', 'Failed to update role permissions.');
        }

        header('Location: /admin/roles');
        exit;
    }

    /**
     * Delete a role.
     */
    public function delete()
    {
        if (!Auth::hasPermission('roles.delete')) {
            $this->forbidden();
        }
        $role_id = $this->route_params['id'];

        if (Role::delete($role_id)) {
            Session::flash('success', 'Role deleted successfully.');
        } else {
            Session::flash('error', 'Failed to delete role. Core roles cannot be deleted.');
        }

        header('Location: /admin/roles');
        exit;
    }

    /**
     * Helper to show a forbidden error.
     */
    private function forbidden()
    {
        Session::flash('error', 'You do not have permission to perform this action.');
        header('Location: /admin/roles');
        exit;
    }
}

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

    public function users($action = 'index', $id = 0) {
        if (!Auth::check('admin.users.manage')) {
            Session::flash('error', 'You do not have permission to manage users.');
            header('Location: /admin');
            exit;
        }

        $userModel = $this->model('User');

        switch ($action) {
            case 'add':
                $roleModel = $this->model('Role');
                $data = [
                    'title' => 'Add New User',
                    'roles' => $roleModel->getAllRoles(),
                    'errors' => []
                ];

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // Basic validation
                    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
                        $data['errors'][] = 'Please fill all required fields.';
                    }
                    if ($userModel->findUserByEmail($_POST['email'])) {
                        $data['errors'][] = 'Email is already taken.';
                    }

                    if (empty($data['errors'])) {
                        if ($userModel->register($_POST)) {
                            Session::flash('success', 'User created successfully.');
                            header('Location: /admin/users');
                            exit;
                        }
                    }
                }
                $this->view('admin/users/add', $data);
                break;

            case 'edit':
                $roleModel = $this->model('Role');
                $data = [
                    'title' => 'Edit User',
                    'user' => $userModel->findUserById($id),
                    'roles' => $roleModel->getAllRoles(),
                    'errors' => []
                ];

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                     if ($userModel->updateUser($id, $_POST)) {
                        Session::flash('success', 'User updated successfully.');
                        header('Location: /admin/users');
                        exit;
                    }
                }
                $this->view('admin/users/edit', $data);
                break;

            case 'delete':
                if ($userModel->deleteUser($id)) {
                    Session::flash('success', 'User deleted successfully.');
                } else {
                    Session::flash('error', 'Could not delete user. The Founder account cannot be deleted.');
                }
                header('Location: /admin/users');
                exit;

            default: // 'index'
                $data = [
                    'title' => 'Manage Users',
                    'users' => $userModel->getAllUsers()
                ];
                $this->view('admin/users/index', $data);
                break;
        }
    }

    public function roles($action = 'index', $id = 0) {
        if (!Auth::check('admin.roles.manage')) {
            Session::flash('error', 'You do not have permission to manage roles.');
            header('Location: /admin');
            exit;
        }

        $roleModel = $this->model('Role');
        $available_permissions = $this->get_available_permissions();

        switch ($action) {
            case 'add':
                $data = [
                    'title' => 'Add New Role',
                    'permissions' => $available_permissions,
                    'errors' => []
                ];

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $permissions = $this->prepare_permissions_from_post($_POST['permissions'] ?? []);
                    if ($roleModel->createRole($_POST['name'], $permissions)) {
                        Session::flash('success', 'Role created successfully.');
                        header('Location: /admin/roles');
                        exit;
                    }
                }
                $this->view('admin/roles/add', $data);
                break;

            case 'edit':
                $role = $roleModel->getRoleById($id);
                $data = [
                    'title' => 'Edit Role',
                    'role' => $role,
                    'role_permissions' => json_decode($role['permissions'], true) ?? [],
                    'permissions' => $available_permissions,
                    'errors' => []
                ];

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $permissions = $this->prepare_permissions_from_post($_POST['permissions'] ?? []);
                    if ($roleModel->updateRole($id, $_POST['name'], $permissions)) {
                        Session::flash('success', 'Role updated successfully.');
                        header('Location: /admin/roles');
                        exit;
                    }
                }
                $this->view('admin/roles/edit', $data);
                break;

            case 'delete':
                if ($roleModel->deleteRole($id)) {
                    Session::flash('success', 'Role deleted successfully.');
                } else {
                    Session::flash('error', 'Could not delete role. Core roles cannot be deleted.');
                }
                header('Location: /admin/roles');
                exit;

            default: // 'index'
                $data = [
                    'title' => 'Manage Roles',
                    'roles' => $roleModel->getAllRoles()
                ];
                $this->view('admin/roles/index', $data);
                break;
        }
    }

    private function get_available_permissions() {
        return [
            'Admin Access' => 'admin.access',
            'Manage Users' => 'admin.users.manage',
            'Manage Roles' => 'admin.roles.manage',
            'Manage Templates' => 'admin.templates.manage',
            'Manage Plugins' => 'admin.plugins.manage',
            'Manage Navigation' => 'admin.links.manage'
        ];
    }

    private function prepare_permissions_from_post($post_permissions) {
        $permissions = [];
        foreach ($post_permissions as $permission) {
            $permissions[$permission] = true;
        }
        return $permissions;
    }

    public function templates($action = 'index', $id = 0) {
        if (!Auth::check('admin.templates.manage')) {
            Session::flash('error', 'You do not have permission to manage templates.');
            header('Location: /admin');
            exit;
        }

        $templateModel = $this->model('Template');

        // Handle Upload
        if ($action === 'upload' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_FILES['template_zip']) && $_FILES['template_zip']['error'] == 0) {
                $target_file = __DIR__ . '/../uploads/' . basename($_FILES['template_zip']['name']);

                // Basic validation
                if ($_FILES['template_zip']['type'] == 'application/zip') {
                    if (move_uploaded_file($_FILES['template_zip']['tmp_name'], $target_file)) {
                        $zip = new ZipArchive;
                        if ($zip->open($target_file) === TRUE) {
                            $zip->extractTo(__DIR__ . '/../../templates/');
                            $zip->close();
                            Session::flash('success', 'Template uploaded and extracted successfully.');
                            unlink($target_file); // Clean up
                        } else {
                            Session::flash('error', 'Failed to open the zip file.');
                        }
                    } else {
                        Session::flash('error', 'Failed to move uploaded file.');
                    }
                } else {
                    Session::flash('error', 'Invalid file type. Only .zip files are allowed.');
                }
            } else {
                Session::flash('error', 'File upload error.');
            }
            header('Location: /admin/templates');
            exit;
        }

        // Sync filesystem templates with DB before any action
        $templateModel->sync_templates();

        if ($action === 'activate' && $id > 0) {
            if ($templateModel->setActiveTemplate($id)) {
                Session::flash('success', 'Template activated successfully.');
            } else {
                Session::flash('error', 'Could not activate template.');
            }
            header('Location: /admin/templates');
            exit;
        }

        // Default 'index' action
        $data = [
            'title' => 'Manage Templates',
            'templates' => $templateModel->getAllTemplates()
        ];
        $this->view('admin/templates/index', $data);
    }

    public function plugins($action = 'index', $plugin_folder = '') {
        if (!Auth::check('admin.plugins.manage')) {
            Session::flash('error', 'You do not have permission to manage plugins.');
            header('Location: /admin');
            exit;
        }

        if ($action === 'upload' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_FILES['plugin_zip']) && $_FILES['plugin_zip']['error'] == 0) {
                $target_file = __DIR__ . '/../uploads/' . basename($_FILES['plugin_zip']['name']);

                if ($_FILES['plugin_zip']['type'] == 'application/zip') {
                    if (move_uploaded_file($_FILES['plugin_zip']['tmp_name'], $target_file)) {
                        $zip = new ZipArchive;
                        if ($zip->open($target_file) === TRUE) {
                            $zip->extractTo(__DIR__ . '/../plugins/');
                            $zip->close();
                            Session::flash('success', 'Plugin uploaded and extracted successfully.');
                            unlink($target_file);
                        } else {
                            Session::flash('error', 'Failed to open the zip file.');
                        }
                    } else {
                        Session::flash('error', 'Failed to move uploaded file.');
                    }
                } else {
                    Session::flash('error', 'Invalid file type. Only .zip files are allowed.');
                }
            } else {
                Session::flash('error', 'File upload error.');
            }
            header('Location: /admin/plugins');
            exit;
        }

        switch ($action) {
            case 'activate':
                if (PluginManager::activate_plugin($plugin_folder)) {
                    Session::flash('success', 'Plugin activated successfully.');
                } else {
                    Session::flash('error', 'Could not activate plugin.');
                }
                header('Location: /admin/plugins');
                exit;

            case 'deactivate':
                if (PluginManager::deactivate_plugin($plugin_folder)) {
                    Session::flash('success', 'Plugin deactivated successfully.');
                } else {
                    Session::flash('error', 'Could not deactivate plugin.');
                }
                header('Location: /admin/plugins');
                exit;

            default: // 'index'
                $data = [
                    'title' => 'Manage Plugins',
                    'plugins' => PluginManager::get_all_plugins(),
                    'active_plugins' => PluginManager::get_active_plugins_from_db()
                ];
                $this->view('admin/plugins/index', $data);
                break;
        }
    }

    public function links($action = 'index', $id = 0) {
        if (!Auth::check('admin.links.manage')) {
            Session::flash('error', 'You do not have permission to manage navigation.');
            header('Location: /admin');
            exit;
        }

        $linkModel = $this->model('Link');
        $languageModel = $this->model('Language');

        switch ($action) {
            case 'add':
                $data = [
                    'title' => 'Add New Link',
                    'links' => $linkModel->getAllLinksForAdmin(),
                    'languages' => $languageModel->getAllLanguages(),
                    'errors' => []
                ];
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if ($linkModel->createLink($_POST)) {
                        Session::flash('success', 'Link created successfully.');
                        header('Location: /admin/links');
                        exit;
                    }
                }
                $this->view('admin/links/add', $data);
                break;

            case 'edit':
                $data = [
                    'title' => 'Edit Link',
                    'link' => $linkModel->getLinkById($id),
                    'links' => $linkModel->getAllLinksForAdmin(),
                    'languages' => $languageModel->getAllLanguages(),
                    'errors' => []
                ];
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if ($linkModel->updateLink($id, $_POST)) {
                        Session::flash('success', 'Link updated successfully.');
                        header('Location: /admin/links');
                        exit;
                    }
                }
                $this->view('admin/links/edit', $data);
                break;

            case 'delete':
                if ($linkModel->deleteLink($id)) {
                    Session::flash('success', 'Link deleted successfully.');
                } else {
                    Session::flash('error', 'Could not delete link.');
                }
                header('Location: /admin/links');
                exit;

            default: // 'index'
                $data = [
                    'title' => 'Manage Navigation',
                    'links' => $linkModel->getAllLinksForAdmin()
                ];
                $this->view('admin/links/index', $data);
                break;
        }
    }
}

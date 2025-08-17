<?php

namespace Plugins\Tasks\Controllers\Admin;

use App\Controllers\Admin\Controller;
use App\Core\View;
use App\Core\Auth;
use App\Core\Session;
use Plugins\Tasks\Models\Task;
use App\Models\User; // To get a list of users to assign tasks to

class TaskController extends Controller
{
    /**
     * Show the main tasks page.
     */
    public function index()
    {
        $tasks = Task::findAll();
        View::render('@tasks_admin/index.php', [
            'title' => 'Task Management',
            'tasks' => $tasks
        ]);
    }

    /**
     * Show the form to create a new task.
     */
    public function new()
    {
        $users = User::getAll(); // Get all users to populate a dropdown
        View::render('@tasks_admin/form.php', [
            'title' => 'New Task',
            'users' => $users
        ]);
    }

    /**
     * Handle creation of a new task.
     */
    public function create()
    {
        $data = [
            'assigned_to_user_id' => $_POST['assigned_to_user_id'],
            'created_by_user_id' => Auth::id(),
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'status' => $_POST['status'],
            'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null
        ];
        Task::create($data);
        Session::flash('success', 'Task created successfully.');
        header('Location: ' . url('admin/tasks'));
        exit;
    }

    /**
     * Show the form to edit a task.
     */
    public function edit()
    {
        $id = $this->route_params['id'];
        $task = Task::findById($id);
        $users = User::getAll();
        View::render('@tasks_admin/form.php', [
            'title' => 'Edit Task',
            'task' => $task,
            'users' => $users
        ]);
    }

    /**
     * Handle updating a task.
     */
    public function update()
    {
        $id = $this->route_params['id'];
        $data = [
            'assigned_to_user_id' => $_POST['assigned_to_user_id'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'status' => $_POST['status'],
            'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null
        ];
        Task::update($id, $data);
        Session::flash('success', 'Task updated successfully.');
        header('Location: ' . url('admin/tasks'));
        exit;
    }

    /**
     * Handle deleting a task.
     */
    public function delete()
    {
        $id = $this->route_params['id'];
        Task::delete($id);
        Session::flash('success', 'Task deleted successfully.');
        header('Location: ' . url('admin/tasks'));
        exit;
    }
}

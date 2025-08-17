<?php

namespace Plugins\Forum\Controllers\Admin;

use App\Controllers\Admin\Controller;
use App\Core\View;
use Plugins\Forum\Models\Forum;

use App\Core\Session;

class ForumController extends Controller
{
    /**
     * Show the main page for managing forums/categories.
     */
    public function index()
    {
        $forums = Forum::getHierarchy();

        View::render('@forum_admin/index.php', [
            'title' => 'Manage Forums',
            'forums' => $forums
        ]);
    }

    public function new()
    {
        $forums = Forum::getHierarchy(); // For parent dropdown
        View::render('@forum_admin/form.php', [
            'title' => 'Add New Forum/Category',
            'forums' => $forums
        ]);
    }

    public function create()
    {
        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null,
            'sort_order' => $_POST['sort_order'] ?? 0
        ];
        Forum::create($data);
        Session::flash('success', 'Forum created successfully.');
        header('Location: ' . url('admin/forum'));
        exit;
    }

    public function edit()
    {
        $id = $this->route_params['id'];
        $forum = Forum::findById($id);
        $forums = Forum::getHierarchy();
        View::render('@forum_admin/form.php', [
            'title' => 'Edit Forum/Category',
            'forum' => $forum,
            'forums' => $forums
        ]);
    }

    public function update()
    {
        $id = $this->route_params['id'];
        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null,
            'sort_order' => $_POST['sort_order'] ?? 0
        ];
        Forum::update($id, $data);
        Session::flash('success', 'Forum updated successfully.');
        header('Location: ' . url('admin/forum'));
        exit;
    }

    public function delete()
    {
        $id = $this->route_params['id'];
        Forum::delete($id);
        Session::flash('success', 'Forum deleted successfully.');
        header('Location: ' . url('admin/forum'));
        exit;
    }
}

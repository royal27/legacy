<?php

namespace Plugins\Downloads\Controllers\Admin;

use App\Controllers\Admin\Controller;
use App\Core\View;

use App\Core\Session;
use Plugins\Downloads\Models\DownloadCategory;
use Plugins\Downloads\Models\DownloadFile;

class DownloadController extends Controller
{
    /**
     * Show the main downloads management dashboard.
     */
    public function index()
    {
        View::render('@downloads_admin/index.php', [
            'title' => 'Downloads Management'
        ]);
    }

    public function files()
    {
        $files = DownloadFile::findAllUnvalidated();
        View::render('@downloads_admin/files.php', [
            'title' => 'Validate Files',
            'files' => $files
        ]);
    }

    public function validateFile()
    {
        $id = $this->route_params['id'];
        DownloadFile::validate($id);
        Session::flash('success', 'File has been validated and is now public.');
        header('Location: ' . url('admin/downloads/files'));
        exit;
    }

    public function deleteFile()
    {
        $id = $this->route_params['id'];
        DownloadFile::delete($id);
        Session::flash('success', 'File has been deleted.');
        header('Location: ' . url('admin/downloads/files'));
        exit;
    }

    public function categories()
    {
        $categories = DownloadCategory::findAll();
        View::render('@downloads_admin/categories.php', [
            'title' => 'Manage Categories',
            'categories' => $categories
        ]);
    }

    public function newCategory()
    {
        View::render('@downloads_admin/category_form.php', [
            'title' => 'Add New Category'
        ]);
    }

    public function createCategory()
    {
        DownloadCategory::create($_POST['name'], $_POST['description']);
        Session::flash('success', 'Category created successfully.');
        header('Location: ' . url('admin/downloads/categories'));
        exit;
    }

    public function editCategory()
    {
        $id = $this->route_params['id'];
        $category = DownloadCategory::findById($id);
        View::render('@downloads_admin/category_form.php', [
            'title' => 'Edit Category',
            'category' => $category
        ]);
    }

    public function updateCategory()
    {
        $id = $this->route_params['id'];
        DownloadCategory::update($id, $_POST['name'], $_POST['description']);
        Session::flash('success', 'Category updated successfully.');
        header('Location: ' . url('admin/downloads/categories'));
        exit;
    }

    public function deleteCategory()
    {
        $id = $this->route_params['id'];
        DownloadCategory::delete($id);
        Session::flash('success', 'Category deleted successfully.');
        header('Location: ' . url('admin/downloads/categories'));
        exit;
    }
}

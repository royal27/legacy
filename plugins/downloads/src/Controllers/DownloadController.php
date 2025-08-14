<?php

namespace Plugins\Downloads\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Core\Auth;
use App\Core\Session;
use Plugins\Downloads\Models\DownloadCategory;
use Plugins\Downloads\Models\DownloadFile;

class DownloadController extends Controller
{
    /**
     * Display the main downloads page (list of categories).
     */
    public function index()
    {
        $categories = DownloadCategory::findAll();
        View::render('@downloads_frontend/index.php', [
            'title' => 'Downloads',
            'categories' => $categories
        ]);
    }

    /**
     * Display files in a specific category.
     */
    public function category()
    {
        $category_id = $this->route_params['id'];
        $files = DownloadFile::findAllByCategory($category_id);
        // We'd also need a Category::findById() method here
        View::render('@downloads_frontend/category.php', [
            'title' => 'Category', // Get category name later
            'files' => $files
        ]);
    }

    /**
     * Show the file upload form.
     */
    public function upload()
    {
        if (!Auth::check()) {
            Session::flash('error', 'You must be logged in to upload files.');
            header('Location: /downloads');
            exit;
        }
        $categories = DownloadCategory::findAll();
        View::render('@downloads_frontend/upload.php', [
            'title' => 'Upload File',
            'categories' => $categories
        ]);
    }

    /**
     * Handle the file upload.
     */
    public function save()
    {
        if (!Auth::check()) {
            Session::flash('error', 'You must be logged in to upload files.');
            header('Location: /downloads');
            exit;
        }

        // --- Basic File Upload Logic ---
        $upload_dir = dirname(__DIR__, 4) . '/public/uploads/downloads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file = $_FILES['download_file'];
        $filename = basename($file['name']);
        $filepath = $upload_dir . uniqid() . '-' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $data = [
                'category_id' => $_POST['category_id'],
                'user_id' => Auth::id(),
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'filename' => $filename,
                'filepath' => str_replace(dirname(__DIR__, 4) . '/public', '', $filepath), // Store relative path
                'filesize' => $file['size']
            ];
            DownloadFile::create($data);
            Session::flash('success', 'File uploaded successfully. It will be visible after admin approval.');
            header('Location: /downloads');
            exit;
        } else {
            Session::flash('error', 'Failed to upload file.');
            header('Location: /downloads/upload');
            exit;
        }
    }

    /**
     * Handle the file download link.
     */
    public function go()
    {
        $file_id = $this->route_params['id'];
        $file = DownloadFile::findById($file_id);

        if ($file) {
            DownloadFile::incrementDownloadCount($file_id);
            $full_path = dirname(__DIR__, 4) . '/public' . $file['filepath'];

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($full_path));
            readfile($full_path);
            exit;
        } else {
            // File not found or not validated
            http_response_code(404);
            die('File not found.');
        }
    }
}

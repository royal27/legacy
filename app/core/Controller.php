<?php

/**
 * Base Controller
 * This loads the models and views
 */
class Controller {
    // Load model
    public function model($model) {
        // Require model file
        require_once __DIR__ . '/../models/' . $model . '.php';
        // Instantiate model
        return new $model();
    }

    // Load view with template
    public function view($view, $data = []) {
        // Get the active template from our global App class
        $active_template = App::$template;

        $view_file = __DIR__ . '/../views/' . $view . '.php';
        $header_file = __DIR__ . '/../../templates/' . $active_template . '/header.php';
        $footer_file = __DIR__ . '/../../templates/' . $active_template . '/footer.php';

        // Check if all necessary files exist
        if (!file_exists($view_file)) {
            die('View does not exist: ' . $view_file);
        }
        if (!file_exists($header_file)) {
            die('Header for template "' . $active_template . '" does not exist: ' . $header_file);
        }
        if (!file_exists($footer_file)) {
            die('Footer for template "' . $active_template . '" does not exist: ' . $footer_file);
        }

        // Include the files
        require_once $header_file;
        require_once $view_file;
        require_once $footer_file;
    }
}

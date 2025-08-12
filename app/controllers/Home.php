<?php

class Home extends Controller {
    public function __construct() {
        // Models can be loaded here
    }

    public function index() {
        // We will now pass translated data to the view
        $data = [
            'title' => Language::get('welcome_title', 'Default Title'),
            'message' => Language::get('welcome_message', 'Default Message')
        ];

        // The view method in the base Controller will load the view file
        $this->view('pages/index', $data);
    }

    // Example of a language switcher
    public function lang($lang_code = 'en') {
        // In a real app, you would validate the lang_code against the database
        $supported_langs = ['en', 'ro'];
        if (in_array($lang_code, $supported_langs)) {
            $_SESSION['language'] = $lang_code;
        }
        // Redirect back to the homepage
        header('Location: ' . '/');
        exit;
    }
}

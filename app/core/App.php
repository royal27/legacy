<?php

/**
 * App class
 * Holds global application settings and state.
 */
class App {
    /**
     * @var string The folder name of the currently active template.
     */
    public static $template = 'default';

    /**
     * @var array Holds the navigation links for the current language.
     */
    public static $menu_links = [];

    /**
     * Initializes application-wide settings.
     * This should be called from init.php.
     */
    public static function init() {
        // Load active template
        $templateModel = new Template();
        $activeTemplate = $templateModel->getActiveTemplate();
        if ($activeTemplate && !empty($activeTemplate['folder_name'])) {
            self::$template = $activeTemplate['folder_name'];
        }

        // Load navigation links
        $linkModel = new Link();
        self::$menu_links = $linkModel->get_menu_links(Language::getCurrentLanguage());
    }
}

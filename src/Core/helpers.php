<?php

if (!function_exists('url')) {
    /**
     * Generate a URL for the application.
     *
     * @param string $path The path to append to the site URL.
     * @return string The full URL.
     */
    function url($path = '')
    {
        // Remove leading/trailing slashes from the path
        $path = trim($path, '/');

        // Use the SITE_URL constant defined in config.php
        $base_url = defined('SITE_URL') ? SITE_URL : '';

        // Check if the base URL already has index.php
        if (strpos($base_url, 'index.php') === false) {
            $base_url .= '/index.php';
        }

        return $base_url . '?route=' . $path;
    }
}

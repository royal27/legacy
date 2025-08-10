## Project Overview

This project is a custom-built website with a dashboard and admin panel. It is designed to be modular and extensible through a plugin system.

## Coding Conventions

*   **PHP:** All PHP code should be written using the MySQLi extension for database interactions and should be compatible with PHP 8.2.12.
*   **Permalinks:** The site uses permalinks. All URLs should be user-friendly.
*   **File Structure:**
    *   `admin/`: Contains the admin panel files.
    *   `assets/`: Contains CSS, JavaScript, and image files.
    *   `includes/`: Contains core files like the database connection, session management, and other helper functions.
    *   `install/`: Contains the installation script.
    *   `plugins/`: Contains the user-installed plugins.
*   **Security:** All user input must be sanitized to prevent XSS and SQL injection attacks.

## Development Workflow

1.  **Installation:** The `install/` directory contains a script to set up the database and create the initial user.
2.  **Frontend:** The frontend is built using a responsive template with a custom color scheme. It uses Toastr for notifications and AJAX for dynamic content.
3.  **Backend:** The backend is written in PHP and uses a custom-built routing system to handle permalinks.
4.  **Plugins:** The plugin system allows for extending the functionality of the site. Each plugin is a self-contained module that can be uploaded and activated through the admin panel.

## Color Palette

*   **Light Mode:** Gradient of violet, blue, and red.
*   **Dark Mode:** Gradient of black, violet, and blue.

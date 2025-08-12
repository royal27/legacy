# PHP Core System

A modular PHP application framework built from the ground up, designed for flexibility and modern development practices.

## Core Features

- **MVC Architecture**: A clean Model-View-Controller pattern to separate concerns.
- **Installer**: A multi-step installation wizard to easily set up the application, database, and admin account.
- **Dynamic Templating**: A template manager that loads the active theme from the database. The default theme includes:
    - Light/Dark mode switcher.
    - Gradient-based color scheme as requested.
- **Multi-Language Support**: A file-based language system (easily extensible) for internationalization. Link titles and other content are translatable.
- **Roles & Permissions**: A robust backend system for creating user roles with specific, JSON-based permissions. Includes a default "Founder" role with full access.
- **Plugin System**: A flexible hooking system with Actions and Filters (`do_action`, `apply_filters`) that allows the application to be extended without modifying core files.
- **Dynamic Navigation**: A link manager that builds the site navigation from the database, with support for translations.
- **Modern Frontend**: Integrated jQuery and Toastr for dynamic JavaScript interactions and notifications.

## How It Works

1.  If the site is not installed, the user is redirected to `/install`.
2.  The installer guides the user through language selection, database setup, and founder account creation.
3.  On each page load, the `app/core/init.php` file bootstraps the application, loading the configuration, database connection, language files, active plugins, and the authentication system.
4.  The `.htaccess` file directs all requests to `index.php`, where the `Router` class parses the URL and calls the appropriate controller method.
5.  The controller interacts with models to get data and then passes that data to a view, which is wrapped by the active template's header and footer.

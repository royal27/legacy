<?php
// --- IP Ban Check ---
// This needs to run before the DB connection is established for the main app,
// so we establish a temporary, early connection here just for this check.
if (file_exists(__DIR__ . '/../config.php')) {
    require_once __DIR__ . '/../config.php';
    $early_db = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$early_db->connect_error) {
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $early_db->prepare("SELECT id FROM banned_ips WHERE ip_address = ?");
        $stmt->bind_param('s', $user_ip);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            http_response_code(403);
            die('Your IP address has been banned.');
        }
        $stmt->close();
        $early_db->close();
    }
}


// Prevent direct file access.
// Although this file is meant to be included, this is a good habit.
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    die('Forbidden');
}

// Ensure the app is installed by checking for config.php
if (!file_exists(__DIR__ . '/../config.php')) {
    die("Configuration file not found. Please run the installer.");
}

// Load configuration
require_once __DIR__ . '/../config.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Turn on error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- Database Connection ---
// Create a new mysqli object and store it in a global variable for easy access.
// Using a global variable is simple for this project's scope.
// For larger projects, a dependency injection container or a service locator would be better.
global $db;
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($db->connect_error) {
    // In a real application, you might want to log this error instead of dying.
    die("Database connection failed: " . $db->connect_error);
}

// Set the character set to utf8mb4 for full Unicode support
$db->set_charset("utf8mb4");

// --- Global Settings ---
// Load all settings from the database into a global array
global $settings;
$settings = [];
$settings_res = $db->query("SELECT name, value FROM settings");
if ($settings_res) {
    while($row = $settings_res->fetch_assoc()) {
        $settings[$row['name']] = $row['value'];
    }
}

// --- Theme System ---
// Define theme path and URL based on active theme setting
$active_theme = $settings['active_theme'] ?? 'default';
if ($active_theme !== 'default' && is_dir(__DIR__ . '/../themes/' . $active_theme)) {
    $settings['theme_url'] = SITE_URL . '/themes/' . $active_theme;
} else {
    // Fallback to default theme assets
    $settings['theme_url'] = SITE_URL . '/assets';
}


// --- Session & Auth Check ---
if (is_logged_in()) {
    $user_id_check = (int)$_SESSION['user_id'];
    $stmt = $db->prepare("SELECT force_logout FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $user_id_check);
    $stmt->execute();
    $user_check_res = $stmt->get_result();

    if ($user_check_res && $user_check_res->num_rows > 0) {
        $user_check = $user_check_res->fetch_assoc();
        if ($user_check['force_logout'] == 1) {
            // Reset the flag
            $stmt_update = $db->prepare("UPDATE users SET force_logout = 0 WHERE id = ?");
            $stmt_update->bind_param('i', $user_id_check);
            $stmt_update->execute();

            // Destroy session and redirect
            session_destroy();
            redirect('login.php?kicked=1');
        }
    }
    $stmt->close();
}


// --- Language System ---
// Load language functions and data
require_once __DIR__ . '/language.php';
load_language();

// --- Plugin System ---
// Load all active plugins
$active_plugins_res = $db->query("SELECT * FROM plugins WHERE is_active = 1");
if ($active_plugins_res) {
    while($plugin = $active_plugins_res->fetch_assoc()) {
        $plugin_dir = __DIR__ . '/../plugins/' . $plugin['identifier'];
        $manifest_path = $plugin_dir . '/plugin.json';
        if (file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);
            if (!empty($manifest['main_file'])) {
                $main_file_path = $plugin_dir . '/' . $manifest['main_file'];
                if (file_exists($main_file_path)) {
                    require_once $main_file_path;
                }
            }
        }
    }
}


// --- Helper Functions (can be expanded later) ---

/**
 * Checks if a user is logged in.
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Checks if the logged-in user is an admin.
 * Assumes role_id 1 is for Admins.
 * @return bool
 */
function is_admin() {
    // An admin is someone who is logged in and has the 'admin_login' permission.
    // Or, as a fallback, has role_id 1.
    return is_logged_in() && (user_has_permission('admin_login') || (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1));
}

/**
 * Loads all permissions for the current user's role into the session.
 * @global mysqli $db
 */
function load_user_permissions() {
    if (!is_logged_in() || isset($_SESSION['permissions'])) {
        return;
    }

    global $db;
    $role_id = (int)($_SESSION['role_id'] ?? 0);
    $_SESSION['permissions'] = [];

    if ($role_id > 0) {
        $stmt = $db->prepare(
            "SELECT p.name FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = ?"
        );
        $stmt->bind_param('i', $role_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $_SESSION['permissions'][] = $row['name'];
        }
        $stmt->close();
    }
}

/**
 * Checks if the current user has a specific permission.
 * @param string $permission_name The name of the permission to check.
 * @return bool
 */
function user_has_permission(string $permission_name): bool {
    // Super Admin (role_id 1) always has all permissions
    if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
        return true;
    }
    return is_logged_in() && in_array($permission_name, $_SESSION['permissions'] ?? []);
}

/**
 * Redirects to a given URL.
 * @param string $url
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Fetches menu items for a specific location.
 * @global mysqli $db
 * @param string $location The menu location identifier.
 * @return array An array of menu items.
 */
function get_menu(string $location): array {
    global $db;
    $location = $db->real_escape_string($location);
    $result = $db->query("SELECT * FROM menu_items WHERE menu_location = '{$location}' ORDER BY sort_order ASC");
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}


// --- CSRF Protection ---
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token() {
    if (!isset($_POST['_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['_token'])) {
        // Token is invalid or missing
        die('CSRF validation failed.');
    }
    // Invalidate the token after use to prevent replay attacks
    unset($_SESSION['csrf_token']);
}

// Generate a token for every GET request page load
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    generate_csrf_token();
}

?>

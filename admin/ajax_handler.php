<?php
define('ADMIN_AREA', true);
require_once __DIR__ . '/../core/bootstrap.php';

// Set header to JSON
header('Content-Type: application/json');

function sanitize_folder_name($name) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
}

// --- Security Check ---
if (!is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication required.']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['status' => 'error', 'message' => 'Invalid action.'];

switch ($action) {
    case 'install_plugin':
        validate_csrf_token();
        if (!class_exists('ZipArchive')) {
            $response['message'] = 'Error: The ZipArchive class is not found. Please enable the Zip PHP extension on your server.';
            break;
        }
        if (isset($_FILES['plugin_zip_file'])) {
            $file = $_FILES['plugin_zip_file'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $response['message'] = 'File upload error. Code: ' . $file['error'];
                break;
            }
            if (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'zip') {
                $response['message'] = 'Invalid file type. Only .zip files are allowed.';
                break;
            }

            $zip = new ZipArchive;
            if ($zip->open($file['tmp_name']) === TRUE) {
                $manifest_json = $zip->getFromName('plugin.json');
                if ($manifest_json === false) {
                    $response['message'] = 'Installation failed: plugin.json not found in the zip archive.';
                    $zip->close();
                    break;
                }

                $manifest = json_decode($manifest_json, true);
                if (json_last_error() !== JSON_ERROR_NONE || empty($manifest['identifier']) || empty($manifest['name'])) {
                    $response['message'] = 'Installation failed: plugin.json is invalid or missing required fields (identifier, name).';
                    $zip->close();
                    break;
                }

                $plugin_identifier = $manifest['identifier'];
                $plugin_dir = __DIR__ . '/../plugins/' . $plugin_identifier;

                if (is_dir($plugin_dir)) {
                    $response['message'] = 'Installation failed: A plugin with this identifier already exists.';
                    $zip->close();
                    break;
                }

                $zip->extractTo($plugin_dir);
                $zip->close();

                $install_sql_path = $plugin_dir . '/install.sql';
                if (file_exists($install_sql_path)) {
                    $db->multi_query(file_get_contents($install_sql_path));
                    while ($db->next_result()) {;}
                }

                $stmt = $db->prepare("INSERT INTO plugins (identifier, name, version, is_active, custom_link) VALUES (?, ?, ?, 0, ?)");
                $stmt->bind_param('ssss', $plugin_identifier, $manifest['name'], $manifest['version'], $manifest['default_link']);
                $stmt->execute();

                $response = ['status' => 'success', 'message' => 'Plugin installed successfully! Reloading...'];
            } else {
                $response['message'] = 'Failed to open zip archive.';
            }
        } else {
            $response['message'] = 'No plugin file was uploaded.';
        }
        break;

    case 'install_from_dir':
        validate_csrf_token();

        $identifier = basename($_POST['identifier'] ?? '');

        if (empty($identifier)) {
            $response['message'] = 'Invalid plugin identifier.';
            break;
        }

        $plugin_dir = __DIR__ . '/../plugins/' . $identifier;
        $manifest_path = $plugin_dir . '/plugin.json';

        if (!is_dir($plugin_dir) || !file_exists($manifest_path)) {
            $response['message'] = 'Plugin directory or manifest not found.';
            break;
        }

        // Check if already installed
        $stmt_check = $db->prepare("SELECT id FROM plugins WHERE identifier = ?");
        $stmt_check->bind_param('s', $identifier);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $response['message'] = 'This plugin is already installed.';
            $stmt_check->close();
            break;
        }
        $stmt_check->close();

        $manifest_json = file_get_contents($manifest_path);
        $manifest = json_decode($manifest_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($manifest['name'])) {
            $response['message'] = 'Installation failed: plugin.json is invalid or missing name.';
            break;
        }

        $install_sql_path = $plugin_dir . '/install.sql';
        if (file_exists($install_sql_path)) {
            $db->multi_query(file_get_contents($install_sql_path));
            while ($db->next_result()) {;} // Clear results
        }

        $version = $manifest['version'] ?? '1.0';
        $default_link = $manifest['default_link'] ?? '';
        $permission_required = $manifest['permission_required'] ?? null;

        $stmt = $db->prepare("INSERT INTO plugins (identifier, name, version, is_active, custom_link, permission_required) VALUES (?, ?, ?, 0, ?, ?)");
        $stmt->bind_param('sssss', $identifier, $manifest['name'], $version, $default_link, $permission_required);

        if ($stmt->execute()) {
             $response = ['status' => 'success', 'message' => 'Plugin installed successfully! Reloading...'];
        } else {
            $response['message'] = 'Failed to insert plugin into the database: ' . $stmt->error;
        }
        $stmt->close();
        break;

    case 'install_theme':
        validate_csrf_token();
        if (!class_exists('ZipArchive')) {
            $response['message'] = 'Error: The ZipArchive class is not found. Please enable the Zip PHP extension on your server.';
            break;
        }
        if (isset($_FILES['theme_zip_file'])) {
            $file = $_FILES['theme_zip_file'];
            if ($file['error'] !== UPLOAD_ERR_OK || pathinfo($file['name'], PATHINFO_EXTENSION) !== 'zip') {
                $response['message'] = 'Invalid file or upload error.';
                break;
            }
            $zip = new ZipArchive;
            if ($zip->open($file['tmp_name']) === TRUE) {
                $manifest_json = $zip->getFromName('theme.json');
                if (!$manifest_json) {
                    $response['message'] = 'theme.json not found.';
                    break;
                }
                $manifest = json_decode($manifest_json, true);
                if (empty($manifest['name'])) {
                    $response['message'] = 'Invalid theme.json.';
                    break;
                }
                $theme_folder = sanitize_folder_name($manifest['name']);
                $theme_dir = __DIR__ . '/../../themes/' . $theme_folder;
                if (is_dir($theme_dir)) {
                    $response['message'] = 'A theme with this name already exists.';
                    break;
                }
                $zip->extractTo($theme_dir);
                $zip->close();
                $response['status'] = 'success';
                $response['message'] = 'Theme installed successfully! Reloading...';
            }
        }
        break;

    case 'moderate_user':
        $user_id = (int)($_POST['user_id'] ?? 0);
        $sub_action = $_POST['sub_action'] ?? '';
        $until = $_POST['until'] ?? null;

        if ($user_id > 0 && !empty($sub_action)) {
            $sql = '';
            $params = [];

            switch ($sub_action) {
                case 'mute_until':
                    $sql = "UPDATE users SET is_muted = 1, muted_until = ? WHERE id = ?";
                    $params = ['si', $until, $user_id];
                    break;
                case 'mute_indefinite':
                    $sql = "UPDATE users SET is_muted = 1, muted_until = NULL WHERE id = ?";
                    $params = ['i', $user_id];
                    break;
                case 'unmute':
                    $sql = "UPDATE users SET is_muted = 0, muted_until = NULL WHERE id = ?";
                    $params = ['i', $user_id];
                    break;
                case 'ban_until':
                    $sql = "UPDATE users SET is_banned = 1, banned_until = ? WHERE id = ?";
                    $params = ['si', $until, $user_id];
                    break;
                case 'ban_indefinite':
                     $sql = "UPDATE users SET is_banned = 1, banned_until = NULL WHERE id = ?";
                    $params = ['i', $user_id];
                    break;
                case 'unban':
                    $sql = "UPDATE users SET is_banned = 0, banned_until = NULL WHERE id = ?";
                    $params = ['i', $user_id];
                    break;
                case 'suspend_user':
                     $sql = "UPDATE users SET suspended_until = ? WHERE id = ?";
                    $params = ['si', $until, $user_id];
                    break;
                case 'unsuspend':
                    $sql = "UPDATE users SET suspended_until = NULL WHERE id = ?";
                    $params = ['i', $user_id];
                    break;
            }

            if (!empty($sql)) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param(...$params);
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'User updated successfully.'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Database error.'];
                }
            } else {
                 $response = ['status' => 'error', 'message' => 'Invalid moderation action.'];
            }
        }
        break;

    case 'delete_theme':
        $folder = basename($_POST['folder'] ?? '');
        if (!empty($folder)) {
            $theme_dir = __DIR__ . '/../../themes/' . $folder;
            // Recursive delete function from plugin handler
            function rrmdir($dir) {
                if (is_dir($dir)) {
                    $objects = scandir($dir);
                    foreach ($objects as $object) {
                        if ($object != "." && $object != "..") {
                            if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir. DIRECTORY_SEPARATOR .$object))
                                rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                            else
                                unlink($dir. DIRECTORY_SEPARATOR .$object);
                        }
                    }
                    rmdir($dir);
                }
            }
            if (is_dir($theme_dir)) {
                rrmdir($theme_dir);
                $response['status'] = 'success';
                $response['message'] = 'Theme deleted successfully!';
            }
        }
        break;

    case 'delete_plugin':
        $plugin_id = (int)($_POST['id'] ?? 0);

        $stmt = $db->prepare("SELECT identifier FROM plugins WHERE id = ?");
        $stmt->bind_param('i', $plugin_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res) {
            $plugin_identifier = $res['identifier'];
            $plugin_dir = __DIR__ . '/../plugins/' . $plugin_identifier;

            // Run uninstall.sql if it exists
            $uninstall_sql_path = $plugin_dir . '/uninstall.sql';
            if (file_exists($uninstall_sql_path)) {
                $sql_content = file_get_contents($uninstall_sql_path);
                $db->multi_query($sql_content);
                while ($db->next_result()) {;}
            }

            // Recursive delete function (ensure it's not declared elsewhere)
            if (!function_exists('rrmdir')) {
                function rrmdir($dir) {
                    if (is_dir($dir)) {
                        $objects = scandir($dir);
                        foreach ($objects as $object) {
                            if ($object != "." && $object != "..") {
                                if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir. DIRECTORY_SEPARATOR .$object))
                                    rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                                else
                                    unlink($dir. DIRECTORY_SEPARATOR .$object);
                            }
                        }
                        rmdir($dir);
                    }
                }
            }
            if (is_dir($plugin_dir)) {
                rrmdir($plugin_dir);
            }

            // Delete from database
            $stmt_delete = $db->prepare("DELETE FROM plugins WHERE id = ?");
            $stmt_delete->bind_param('i', $plugin_id);
            $stmt_delete->execute();

            $response['status'] = 'success';
            $response['message'] = 'Plugin deleted successfully!';
        } else {
            $response['message'] = 'Plugin not found.';
        }
        break;

    case 'kick_user':
        $user_to_kick = (int)($_POST['user_id'] ?? 0);
        if ($user_to_kick > 0 && $user_to_kick !== $_SESSION['user_id']) {
            $stmt = $db->prepare("UPDATE users SET force_logout = 1 WHERE id = ?");
            $stmt->bind_param('i', $user_to_kick);
            $stmt->execute();
            $response['status'] = 'success';
            $response['message'] = 'User has been marked for forced logout. They will be logged out on their next page load.';
        } else {
            $response['message'] = 'Invalid user ID or you cannot kick yourself.';
        }
        break;

    case 'upload_logo':
        if (isset($_FILES['logo_image'])) {
            $file = $_FILES['logo_image'];

            // File validation
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $response['message'] = 'File upload error. Code: ' . $file['error'];
                break;
            }
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowed_types)) {
                $response['message'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
                break;
            }
            if ($file['size'] > 2097152) { // 2 MB
                $response['message'] = 'File is too large. Maximum size is 2MB.';
                break;
            }

            // Generate a unique name and move the file
            $upload_dir = __DIR__ . '/../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'logo_' . uniqid() . '.' . $extension;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Update database
                $stmt1 = $db->prepare("UPDATE settings SET value = ? WHERE name = 'logo_image'");
                $stmt1->bind_param('s', $new_filename);
                $stmt1->execute();

                $stmt2 = $db->prepare("UPDATE settings SET value = 'image' WHERE name = 'logo_type'");
                $stmt2->execute();

                $response['status'] = 'success';
                $response['message'] = 'Logo uploaded successfully!';
                $response['filepath'] = $new_filename;
            } else {
                $response['message'] = 'Failed to move uploaded file. Check permissions.';
            }
        } else {
            $response['message'] = 'No file was uploaded.';
        }
        break;

    case 'delete_logo':
        // Get current logo to delete it from the server
        $res = $db->query("SELECT value FROM settings WHERE name = 'logo_image'")->fetch_assoc();
        $current_logo = $res['value'];

        if (!empty($current_logo)) {
            $file_path = __DIR__ . '/../uploads/' . $current_logo;
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
        }

        // Update database to revert to text logo
        $stmt1 = $db->prepare("UPDATE settings SET value = '' WHERE name = 'logo_image'");
        $stmt1->execute();

        $stmt2 = $db->prepare("UPDATE settings SET value = 'text' WHERE name = 'logo_type'");
        $stmt2->execute();

        $response['status'] = 'success';
        $response['message'] = 'Logo image deleted successfully. Switched to text logo.';
        break;
}

echo json_encode($response);
exit;
?>

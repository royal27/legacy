<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

// Security Check
if (!user_has_permission('manage_plugins')) {
    echo '<div class="message-box error">You do not have permission to manage plugins.</div>';
    return;
}

$message = '';
$message_type = '';

// --- Handle POST actions from this page ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token();
    $action = $_POST['action'] ?? '';

    // --- Handle Plugin Install ---
    if ($action === 'install_plugin') {
        if (!class_exists('ZipArchive')) {
            $message = 'Error: The ZipArchive class is not found. Please enable the Zip PHP extension on your server.';
            $message_type = 'error';
        } elseif (isset($_FILES['plugin_zip_file']) && $_FILES['plugin_zip_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['plugin_zip_file'];

            if (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'zip') {
                $message = 'Invalid file type. Only .zip files are allowed.';
                $message_type = 'error';
            } else {
                $zip = new ZipArchive;
                if ($zip->open($file['tmp_name']) === TRUE) {
                    $manifest_json = $zip->getFromName('plugin.json');
                    if ($manifest_json === false) {
                        $message = 'Installation failed: plugin.json not found in the zip archive.';
                        $message_type = 'error';
                    } else {
                        $manifest = json_decode($manifest_json, true);
                        if (json_last_error() !== JSON_ERROR_NONE || empty($manifest['identifier']) || empty($manifest['name'])) {
                            $message = 'Installation failed: plugin.json is invalid or missing required fields (identifier, name).';
                            $message_type = 'error';
                        } else {
                            $plugin_identifier = $manifest['identifier'];
                            $plugin_dir = __DIR__ . '/../../plugins/' . $plugin_identifier;

                            if (is_dir($plugin_dir)) {
                                $message = 'Installation failed: A plugin with this identifier already exists.';
                                $message_type = 'error';
                            } else {
                                $zip->extractTo($plugin_dir);
                                $zip->close();

                                $install_sql_path = $plugin_dir . '/install.sql';
                                if (file_exists($install_sql_path)) {
                                    $db->multi_query(file_get_contents($install_sql_path));
                                    while ($db->next_result()) {;} // Clear results
                                }

                                $stmt = $db->prepare("INSERT INTO plugins (identifier, name, version, is_active, custom_link) VALUES (?, ?, ?, 0, ?)");
                                $stmt->bind_param('ssss', $plugin_identifier, $manifest['name'], $manifest['version'], $manifest['default_link']);
                                $stmt->execute();

                                $message = 'Plugin installed successfully!';
                                $message_type = 'success';
                            }
                        }
                    }
                    if ($message_type !== 'success') $zip->close();
                } else {
                    $message = 'Failed to open zip archive.';
                    $message_type = 'error';
                }
            }
        } else {
            $message = 'No plugin file was uploaded or an upload error occurred.';
            $message_type = 'error';
        }
    }

    // Activate/Deactivate
    if ($action === 'toggle_active') {
        $plugin_id = (int)$_POST['plugin_id'];
        $new_status = (int)$_POST['is_active'];
        $stmt = $db->prepare("UPDATE plugins SET is_active = ? WHERE id = ?");
        $stmt->bind_param('ii', $new_status, $plugin_id);
        $stmt->execute();
        $stmt->close();
        $message = 'Plugin status updated.';
        $message_type = 'success';
    }
    // Edit Link/Name
    if(isset($_POST['action']) && $_POST['action'] === 'edit_plugin') {
        $plugin_id = (int)$_POST['plugin_id'];
        $name = trim($_POST['name']);
        $link = trim($_POST['custom_link']);
        $permission = trim($_POST['permission_required']);
        $stmt = $db->prepare("UPDATE plugins SET name = ?, custom_link = ?, permission_required = ? WHERE id = ?");
        $stmt->bind_param('sssi', $name, $link, $permission, $plugin_id);
        $stmt->execute();
        $stmt->close();
        $message = 'Plugin details updated.';
        $message_type = 'success';
    }
}


// --- Fetch all installed plugins ---
$plugins = $db->query("SELECT * FROM plugins ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

?>

<?php if ($message): ?>
<div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="content-block">
    <h2>Install New Plugin</h2>
    <p>Upload a plugin in .zip format. The zip file must contain a <strong>plugin.json</strong> manifest file.</p>
    <form action="index.php?page=plugins" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="install_plugin">
        <div class="form-group">
            <label for="plugin_zip_file">Plugin .zip file</label>
            <input type="file" id="plugin_zip_file" name="plugin_zip_file" accept=".zip" required>
        </div>
        <button type="submit" class="btn btn-secondary">Upload & Install</button>
    </form>
</div>


<div class="content-block">
    <h2>Installed Plugins</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Plugin</th>
                <th>Status</th>
                <th>Custom Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($plugins)): ?>
                <tr><td colspan="4">No plugins are currently installed.</td></tr>
            <?php endif; ?>
            <?php foreach ($plugins as $plugin): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($plugin['name']); ?></strong>
                    <small style="display:block; color: #6c757d;">v<?php echo htmlspecialchars($plugin['version']); ?> | ID: <?php echo htmlspecialchars($plugin['identifier']); ?></small>
                </td>
                <td>
                    <form action="index.php?page=plugins" method="post" class="toggle-form">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="toggle_active">
                        <input type="hidden" name="plugin_id" value="<?php echo $plugin['id']; ?>">
                        <input type="hidden" name="is_active" value="<?php echo $plugin['is_active'] ? '0' : '1'; ?>">
                        <button type="submit" class="status-toggle <?php echo $plugin['is_active'] ? 'active' : ''; ?>">
                            <?php echo $plugin['is_active'] ? 'Active' : 'Inactive'; ?>
                        </button>
                    </form>
                </td>
                <td>
                    <div class="link-container">
                        <input type="text" value="<?php echo htmlspecialchars($plugin['custom_link'] ?? ''); ?>" readonly>
                        <button class="btn btn-sm copy-btn">Copy</button>
                    </div>
                </td>
                <td>
                    <button class="btn btn-primary btn-sm edit-plugin-btn" data-id="<?php echo $plugin['id']; ?>" data-name="<?php echo htmlspecialchars($plugin['name']); ?>" data-link="<?php echo htmlspecialchars($plugin['custom_link'] ?? ''); ?>" data-permission="<?php echo htmlspecialchars($plugin['permission_required'] ?? ''); ?>">Edit</button>
                    <button class="btn btn-accent btn-sm delete-plugin-btn" data-id="<?php echo $plugin['id']; ?>">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Edit Plugin Modal -->
<div id="edit-plugin-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Edit Plugin</h2>
        <form id="edit-plugin-form" action="index.php?page=plugins" method="post">
            <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="edit_plugin">
            <input type="hidden" id="edit-plugin-id" name="plugin_id">
            <div class="form-group">
                <label for="edit-plugin-name">Plugin Name</label>
                <input type="text" id="edit-plugin-name" name="name" required>
            </div>
            <div class="form-group">
                <label for="edit-plugin-link">Custom Link</label>
                <input type="text" id="edit-plugin-link" name="custom_link">
            </div>
            <div class="form-group">
                <label for="edit-plugin-permission">Permission Required (Optional)</label>
                <input type="text" id="edit-plugin-permission" name="permission_required">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<style>
.status-toggle { border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; color: white; }
.status-toggle.active { background-color: var(--color-primary); }
.status-toggle:not(.active) { background-color: #6c757d; }
.link-container { display: flex; }
.link-container input { flex-grow: 1; }
.modal { position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
.modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 5px; position: relative; }
.close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
</style>

<script>
$(document).ready(function() {
    console.log("Plugins page JS loaded. Uploader script is isolated for debugging.");

    /*
    // --- Modal Logic ---
    var modal = $('#edit-plugin-modal');
    $('.edit-plugin-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var link = $(this).data('link');
        var permission = $(this).data('permission');
        $('#edit-plugin-id').val(id);
        $('#edit-plugin-name').val(name);
        $('#edit-plugin-link').val(link);
        $('#edit-plugin-permission').val(permission);
        modal.show();
    });
    $('.close-btn').on('click', function() {
        modal.hide();
    });
    $(window).on('click', function(event) {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
    });

    // --- Copy Link Logic ---
    $('.copy-btn').on('click', function() {
        var input = $(this).siblings('input')[0];
        input.select();
        document.execCommand('copy');
        toastr.success('Link copied to clipboard!');
    });

    // --- Delete Plugin Logic ---
    $('.delete-plugin-btn').on('click', function() {
        if (!confirm('Are you sure you want to delete this plugin? This will remove all its files and run its uninstall script.')) {
            return;
        }
        var button = $(this);
        var plugin_id = button.data('id');

        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'delete_plugin', id: plugin_id },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    button.closest('tr').fadeOut(500, function() { $(this).remove(); });
                } else {
                    toastr.error(response.message || 'An error occurred.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Delete plugin AJAX error:", textStatus, errorThrown);
                toastr.error('An unexpected error occurred.');
            }
        });
    });
    */

    // The uploader logic has been moved to a standard form submission.
});
</script>

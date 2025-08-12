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

// --- Handle POST actions from this page (e.g., activate/deactivate, edit) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token();
    // Activate/Deactivate
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_active') {
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
    <form id="upload-plugin-form" method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="plugin_zip_file">Plugin .zip file</label>
            <input type="file" id="plugin_zip_file" name="plugin_zip_file" accept=".zip" required>
        </div>
        <div class="progress-bar-container" style="display: none; margin-top: 10px;">
            <div class="progress-bar"></div>
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
    console.log("Plugins page JS loaded.");

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

    // --- Install Plugin Logic ---
    $('#upload-plugin-form').on('submit', function(e) {
        console.log("Upload form submitted.");
        e.preventDefault();

        var fileInput = $('#plugin_zip_file')[0];
        if (!fileInput.files || fileInput.files.length === 0) {
            toastr.error("Please select a file to upload.");
            return;
        }

        var formData = new FormData(this);
        formData.append('action', 'install_plugin');

        var progressBarContainer = $('.progress-bar-container');
        var progressBar = $('.progress-bar');

        console.log("Sending AJAX request to install plugin...");
        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                progressBarContainer.show();
                progressBar.width('0%').text('0%');
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = parseInt((evt.loaded / evt.total) * 100);
                        progressBar.width(percentComplete + '%');
                        progressBar.text(percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                console.log("Install plugin AJAX success:", response);
                progressBarContainer.hide();
                if (response.status === 'success') {
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    toastr.error(response.message || 'An error occurred during installation.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Install plugin AJAX error:", textStatus, errorThrown);
                progressBarContainer.hide();
                toastr.error('An unexpected error occurred during upload. Check console for details.');
            }
        });
    });
});
</script>

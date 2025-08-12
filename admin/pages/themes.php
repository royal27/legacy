<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

if (!user_has_permission('manage_settings')) {
    echo '<div class="message-box error">You do not have permission to manage themes.</div>';
    return;
}

$message = '';
$message_type = '';
$themes_dir = __DIR__ . '/../../themes/';

// --- Handle POST actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token();
    $action = $_POST['action'] ?? '';

    // --- Handle Theme Install ---
    if ($action === 'install_theme') {
        if (!class_exists('ZipArchive')) {
            $message = 'Error: The ZipArchive class is not found. Please enable the Zip PHP extension on your server.';
            $message_type = 'error';
        } elseif (isset($_FILES['theme_zip_file']) && $_FILES['theme_zip_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['theme_zip_file'];
            if (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'zip') {
                $message = 'Invalid file type. Only .zip files are allowed.';
                $message_type = 'error';
            } else {
                $zip = new ZipArchive;
                if ($zip->open($file['tmp_name']) === TRUE) {
                    $manifest_json = $zip->getFromName('theme.json');
                    if (!$manifest_json) {
                        $message = 'theme.json not found.';
                        $message_type = 'error';
                    } else {
                        $manifest = json_decode($manifest_json, true);
                        if (empty($manifest['name'])) {
                            $message = 'Invalid theme.json.';
                            $message_type = 'error';
                        } else {
                            $theme_folder = sanitize_folder_name($manifest['name']);
                            $theme_dir = __DIR__ . '/../../themes/' . $theme_folder;
                            if (is_dir($theme_dir)) {
                                $message = 'A theme with this name already exists.';
                                $message_type = 'error';
                            } else {
                                $zip->extractTo($theme_dir);
                                $message = 'Theme installed successfully!';
                                $message_type = 'success';
                            }
                        }
                    }
                    $zip->close();
                } else {
                    $message = 'Failed to open zip archive.';
                    $message_type = 'error';
                }
            }
        } else {
            $message = 'No theme file was uploaded or an upload error occurred.';
            $message_type = 'error';
        }
    }

    // Activate Theme
    if ($action === 'activate_theme') {
        $theme_folder = basename($_POST['theme_folder']); // Sanitize input

        // Update setting in DB
        $stmt = $db->prepare("INSERT INTO settings (name, value) VALUES ('active_theme', ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
        $stmt->bind_param('s', $theme_folder);
        $stmt->execute();
        $stmt->close();

        $message = 'Theme activated successfully.';
        $message_type = 'success';
        // Refresh settings global
        $settings['active_theme'] = $theme_folder;
    }
}


// --- Scan for themes ---
$themes = [];
if (is_dir($themes_dir)) {
    $dir_contents = scandir($themes_dir);
    foreach ($dir_contents as $item) {
        $theme_path = $themes_dir . $item;
        $manifest_path = $theme_path . '/theme.json';
        if (is_dir($theme_path) && $item != '.' && $item != '..' && file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);
            if ($manifest) {
                $themes[$item] = $manifest;
            }
        }
    }
}

$active_theme = $settings['active_theme'] ?? 'default';

?>

<?php if ($message): ?>
<div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="content-block">
    <h2>Install New Theme</h2>
    <p>Upload a theme in .zip format. The zip file must contain a <strong>theme.json</strong> manifest file.</p>
    <form action="index.php?page=themes" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="install_theme">
        <div class="form-group">
            <label for="theme_zip_file">Theme .zip file</label>
            <input type="file" id="theme_zip_file" name="theme_zip_file" accept=".zip" required>
        </div>
        <button type="submit" class="btn btn-secondary">Upload & Install Theme</button>
    </form>
</div>

<div class="content-block">
    <h2>Installed Themes</h2>
    <div class="themes-grid">
        <!-- Default Theme -->
        <div class="theme-card <?php echo ($active_theme === 'default') ? 'active' : ''; ?>">
            <div class="theme-card-header">
                <h3>Default Theme</h3>
                <small>The core theme</small>
            </div>
            <div class="theme-card-footer">
                <?php if ($active_theme === 'default'): ?>
                    <span class="btn btn-sm btn-success">Active</span>
                <?php else: ?>
                    <form action="index.php?page=themes" method="post" style="display:inline;">
                        <input type="hidden" name="action" value="activate_theme">
                        <input type="hidden" name="theme_folder" value="default">
                        <button type="submit" class="btn btn-primary btn-sm">Activate</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Custom Themes -->
        <?php foreach ($themes as $folder => $manifest): ?>
        <div class="theme-card <?php echo ($active_theme === $folder) ? 'active' : ''; ?>">
             <div class="theme-card-header">
                <h3><?php echo htmlspecialchars($manifest['name']); ?></h3>
                <small>v<?php echo htmlspecialchars($manifest['version']); ?> by <?php echo htmlspecialchars($manifest['author']); ?></small>
            </div>
            <div class="theme-card-footer">
                <?php if ($active_theme === $folder): ?>
                    <span class="btn btn-sm btn-success">Active</span>
                <?php else: ?>
                     <form action="index.php?page=themes" method="post" style="display:inline;">
                        <input type="hidden" name="action" value="activate_theme">
                        <input type="hidden" name="theme_folder" value="<?php echo htmlspecialchars($folder); ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Activate</button>
                    </form>
                <?php endif; ?>
                 <button class="btn btn-accent btn-sm delete-theme-btn" data-folder="<?php echo htmlspecialchars($folder); ?>">Delete</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.themes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
.theme-card { background: #fff; border: 1px solid #ddd; border-radius: 5px; display: flex; flex-direction: column; }
.theme-card.active { border-color: var(--color-primary); border-width: 2px; }
.theme-card-header { padding: 15px; flex-grow: 1; }
.theme-card-header h3 { margin: 0; }
.theme-card-footer { background: #f8f9fa; padding: 10px; border-top: 1px solid #ddd; }
.btn-success { background-color: #28a745; border-color: #28a745; }
</style>

<script>
$(document).ready(function() {
    console.log("Themes page JS loaded. Uploader script is isolated for debugging.");
    /*
    // --- Delete Theme Logic ---
    $('.delete-theme-btn').on('click', function() {
        if (!confirm('Are you sure you want to delete this theme? This action cannot be undone.')) {
            return;
        }
        var button = $(this);
        var folder = button.data('folder');

        $.ajax({
            url: 'ajax_handler.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'delete_theme', folder: folder },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    button.closest('.theme-card').fadeOut(500, function() { $(this).remove(); });
                } else {
                    toastr.error(response.message || 'An error occurred.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Delete theme AJAX error:", textStatus, errorThrown);
                toastr.error('An unexpected error occurred.');
            }
        });
    });
    */

    // Uploader logic moved to standard form submission
});
</script>

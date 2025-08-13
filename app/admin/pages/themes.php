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
    <form id="upload-theme-form" method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group">
            <label for="theme_zip_file">Theme .zip file</label>
            <input type="file" id="theme_zip_file" name="theme_zip_file" accept=".zip" required>
        </div>
        <div class="progress-bar-container" style="display: none; margin-top: 10px;">
            <div class="progress-bar"></div>
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
jQuery(document).ready(function($) {
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

    // --- Install Theme Logic ---
    $('#upload-theme-form').on('submit', function(e) {
        e.preventDefault();
        var fileInput = $('#theme_zip_file')[0];
        if (!fileInput.files || fileInput.files.length === 0) {
            toastr.error("Please select a file to upload.");
            return;
        }
        var formData = new FormData(this);
        formData.append('action', 'install_theme');

        var progressBarContainer = $('.progress-bar-container');
        var progressBar = $('.progress-bar');

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
                progressBarContainer.hide();
                if (response.status === 'success') {
                    toastr.success(response.message);
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    toastr.error(response.message || 'An error occurred during installation.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                progressBarContainer.hide();
                toastr.error('Upload failed. Check the developer console (F12) for more details.');
                console.error("Install theme AJAX error:", textStatus, errorThrown, jqXHR.responseText);
            }
        });
    });
});
</script>

<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

$message = '';
$message_type = '';

// --- Handle standard POST form submission for text-based settings ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_text_settings') {
    validate_csrf_token();
    // A whitelist of settings that can be updated via this form
    $allowed_settings = [
        'site_title',
        'logo_type',
        'logo_text',
        'footer_text',
        'homepage_display'
    ];

    $db->begin_transaction();
    try {
        $stmt = $db->prepare("UPDATE settings SET value = ? WHERE name = ?");

        foreach ($allowed_settings as $setting_name) {
            if (isset($_POST[$setting_name])) {
                $value = trim($_POST[$setting_name]);
                $stmt->bind_param('ss', $value, $setting_name);
                $stmt->execute();
            }
        }
        $stmt->close();
        $db->commit();
        $message = 'Settings updated successfully.';
        $message_type = 'success';
    } catch (mysqli_sql_exception $exception) {
        $db->rollback();
        $message = 'Error updating settings: ' . $exception->getMessage();
        $message_type = 'error';
    }
}


// --- Fetch all settings from DB to populate the form ---
$settings_res = $db->query("SELECT * FROM settings");
$settings = [];
while($row = $settings_res->fetch_assoc()) {
    $settings[$row['name']] = $row['value'];
}

// Fetch pages for the homepage dropdown
$pages = $db->query("SELECT id, title, slug FROM pages ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

?>

<?php if ($message): ?>
<div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="content-block">
    <form action="index.php?page=settings" method="post" id="text-settings-form">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="update_text_settings">

        <h2>General Settings</h2>
        <div class="form-group">
            <label for="site_title">Site Title</label>
            <input type="text" id="site_title" name="site_title" value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="footer_text">Footer Text (accepts basic HTML)</label>
            <textarea id="footer_text" name="footer_text" rows="4"><?php echo htmlspecialchars($settings['footer_text'] ?? ''); ?></textarea>
        </div>

        <hr>

        <h2>Homepage Settings</h2>
        <div class="form-group">
            <label for="homepage_display">Display on Homepage</label>
            <select id="homepage_display" name="homepage_display">
                <option value="default" <?php echo (($settings['homepage_display'] ?? 'default') === 'default') ? 'selected' : ''; ?>>Default Homepage</option>
                <optgroup label="Pages">
                    <?php foreach ($pages as $page): ?>
                        <option value="page-<?php echo $page['id']; ?>" <?php echo (($settings['homepage_display'] ?? '') === 'page-'.$page['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($page['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
                <!-- Plugin pages can be added here later -->
            </select>
            <small>Choose what to show on the front page.</small>
        </div>

        <hr>

        <h2>Logo Settings</h2>
        <div class="form-group">
            <label>Logo Type</label>
            <div>
                <label><input type="radio" name="logo_type" value="text" <?php echo ($settings['logo_type'] === 'text') ? 'checked' : ''; ?>> Text</label>
                <label style="margin-left: 20px;"><input type="radio" name="logo_type" value="image" <?php echo ($settings['logo_type'] === 'image') ? 'checked' : ''; ?>> Image</label>
            </div>
        </div>

        <div id="logo-text-field">
            <div class="form-group">
                <label for="logo_text">Logo Text</label>
                <input type="text" id="logo_text" name="logo_text" value="<?php echo htmlspecialchars($settings['logo_text'] ?? ''); ?>">
                <small>This text will be displayed with the special handwritten font.</small>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Text Settings</button>
    </form>
</div>

<div class="content-block" id="logo-image-field">
    <h3>Upload Logo Image</h3>
    <div class="form-group">
        <label for="logo_image_upload">Choose a new logo image (e.g., PNG, JPG, GIF)</label>
        <input type="file" id="logo_image_upload" name="logo_image">
        <div class="progress-bar-container" style="display: none; margin-top: 10px;">
            <div class="progress-bar"></div>
        </div>
    </div>
    <div id="logo-preview">
        <?php if (!empty($settings['logo_image'])): ?>
            <p>Current Logo:</p>
            <img src="../uploads/<?php echo htmlspecialchars($settings['logo_image']); ?>?t=<?php echo time(); ?>" alt="Current Logo" style="max-width: 200px; background: #eee; padding: 10px;">
            <button id="delete-logo-btn" class="btn btn-accent btn-sm" style="display:block; margin-top:10px;">Delete Image</button>
        <?php else: ?>
            <p>No image logo is currently uploaded.</p>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // --- Toggle logo fields based on radio button selection ---
    function toggleLogoFields() {
        if ($('input[name="logo_type"]:checked').val() === 'text') {
            $('#logo-text-field').show();
            $('#logo-image-field').hide();
        } else {
            $('#logo-text-field').hide();
            $('#logo-image-field').show();
        }
    }
    // Initial call
    toggleLogoFields();
    // Bind to change event
    $('input[name="logo_type"]').on('change', toggleLogoFields);

    // --- AJAX for deleting the logo ---
    $('#delete-logo-btn').on('click', function() {
        if (!confirm('Are you sure you want to delete the logo image?')) {
            return;
        }

        $.ajax({
            url: 'ajax_handler.php', // We will create this file
            type: 'POST',
            dataType: 'json',
            data: { action: 'delete_logo' },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    // Revert to text logo on success
                    $('input[name="logo_type"][value="text"]').prop('checked', true).change();
                    $('#logo-preview').html('<p>No image logo is currently uploaded.</p>');
                } else {
                    toastr.error(response.message || 'An error occurred.');
                }
            },
            error: function() {
                toastr.error('An unexpected error occurred.');
            }
        });
    });

    // --- AJAX for uploading the logo ---
    $('#logo_image_upload').on('change', function() {
        var file_data = $(this).prop('files')[0];
        if (!file_data) {
            return;
        }
        var form_data = new FormData();
        form_data.append('logo_image', file_data);
        form_data.append('action', 'upload_logo');

        var progressBarContainer = $('.progress-bar-container');
        var progressBar = $('.progress-bar');

        $.ajax({
            url: 'ajax_handler.php', // We will create this file
            type: 'POST',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                progressBarContainer.show();
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);
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
                    // Update the preview
                    var newImageUrl = '../uploads/' + response.filepath + '?t=' + new Date().getTime();
                    $('#logo-preview').html(
                        '<p>Current Logo:</p>' +
                        '<img src="' + newImageUrl + '" alt="Current Logo" style="max-width: 200px; background: #eee; padding: 10px;">' +
                        '<button id="delete-logo-btn" class="btn btn-accent btn-sm" style="display:block; margin-top:10px;">Delete Image</button>'
                    );
                    // Clear the file input
                    $('#logo_image_upload').val('');
                } else {
                    toastr.error(response.message || 'An error occurred.');
                }
            },
            error: function() {
                progressBarContainer.hide();
                toastr.error('An unexpected error occurred during upload.');
            }
        });
    });
});
</script>

<style>
.progress-bar-container {
    width: 100%;
    background-color: #f3f3f3;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.progress-bar {
    width: 0%;
    height: 20px;
    background-color: var(--color-primary);
    text-align: center;
    line-height: 20px;
    color: white;
    border-radius: 5px;
    transition: width 0.4s ease;
}
</style>

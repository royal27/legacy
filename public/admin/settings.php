<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/models/setting.php';

$conn = db_connect();

// --- FORM PROCESSING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Update General Settings ---
    if (isset($_POST['update_general_settings'])) {
        update_setting($conn, 'site_name', $_POST['site_name'] ?? '');
        update_setting($conn, 'default_font', $_POST['default_font'] ?? 'cursive');
        update_setting($conn, 'footer_text', $_POST['footer_text'] ?? '');
        $_SESSION['success_message'] = 'General settings updated successfully.';
    }

    // --- Upload Logo ---
    if (isset($_POST['upload_logo'])) {
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['logo'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
            if (in_array($file['type'], $allowed_types) && $file['size'] < 2000000) { // 2MB limit
                // Delete old logo if it exists
                $old_logo_path = get_all_settings($conn)['site_logo_path'] ?? '';
                if (!empty($old_logo_path) && file_exists(__DIR__ . '/../uploads/' . $old_logo_path)) {
                    unlink(__DIR__ . '/../uploads/' . $old_logo_path);
                }

                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_filename = 'logo_' . time() . '.' . $extension;
                $upload_path = __DIR__ . '/../uploads/' . $new_filename;

                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    update_setting($conn, 'site_logo_path', $new_filename);
                    $_SESSION['success_message'] = 'Logo uploaded successfully.';
                } else {
                    $_SESSION['errors'][] = 'Failed to move uploaded file.';
                }
            } else {
                $_SESSION['errors'][] = 'Invalid file type or size too large.';
            }
        } else {
            $_SESSION['errors'][] = 'An error occurred during file upload.';
        }
    }

    // --- Delete Logo ---
    if (isset($_POST['delete_logo'])) {
        $logo_path = get_all_settings($conn)['site_logo_path'] ?? '';
        if (!empty($logo_path) && file_exists(__DIR__ . '/../uploads/' . $logo_path)) {
            unlink(__DIR__ . '/../uploads/' . $logo_path);
        }
        update_setting($conn, 'site_logo_path', '');
        $_SESSION['success_message'] = 'Logo deleted successfully.';
    }

    // --- Update Footer Links ---
    if (isset($_POST['update_footer_links'])) {
        $footer_links = $_POST['footer_links'] ?? [];
        $sanitized_links = [];
        foreach ($footer_links as $link) {
            if (!empty($link['text']) && !empty($link['url'])) {
                $sanitized_links[] = [
                    'text' => htmlspecialchars($link['text']),
                    'url' => filter_var($link['url'], FILTER_SANITIZE_URL)
                ];
            }
        }
        update_setting($conn, 'footer_links', json_encode($sanitized_links));
        $_SESSION['success_message'] = 'Footer links updated successfully.';
    }

    header("Location: settings.php");
    exit();
}

$settings = get_all_settings($conn);
$conn->close();
?>

<?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>

<h1>Site Settings</h1>
<p>Manage global settings for the entire website. These settings will be reflected on the public-facing pages.</p>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['errors'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars(implode(', ', $_SESSION['errors'])) . '</div>';
    unset($_SESSION['errors']);
}
?>

<div class="content-box">
    <h2>General Settings</h2>
    <form action="settings.php" method="POST" class="admin-form" style="max-width:100%">
        <div class="form-group">
            <label for="site_name">Site Name</label>
            <input type="text" id="site_name" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="default_font">Default Font Family</label>
            <input type="text" id="default_font" name="default_font" class="form-control" value="<?php echo htmlspecialchars($settings['default_font'] ?? 'cursive'); ?>">
            <small>Enter a font family name (e.g., 'Dancing Script', cursive). Make sure the font is imported if it's a webfont.</small>
        </div>
        <div class="form-group">
            <label for="footer_text">Footer Text</label>
            <textarea id="footer_text" name="footer_text" class="form-control" rows="3"><?php echo htmlspecialchars($settings['footer_text'] ?? ''); ?></textarea>
        </div>
        <button type="submit" name="update_general_settings" class="btn btn-primary">Save General Settings</button>
    </form>
</div>

<div class="content-box" style="margin-top: 2rem;">
    <h2>Logo Management</h2>
    <form action="settings.php" method="POST" enctype="multipart/form-data" class="admin-form" style="max-width:100%">
        <div class="current-logo">
            <?php if (!empty($settings['site_logo_path'])): ?>
                <p><strong>Current Logo:</strong></p>
                <img src="/uploads/<?php echo htmlspecialchars($settings['site_logo_path']); ?>" alt="Site Logo" style="max-width: 250px; background: #f0f0f0; padding: 10px; border-radius: 5px; margin-bottom: 1rem;">
                <br>
                <button type="submit" name="delete_logo" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete the logo?')">Delete Logo</button>
            <?php else: ?>
                <p>No logo has been uploaded. The site name will be used as text.</p>
            <?php endif; ?>
        </div>
        <hr style="margin: 2rem 0;">
        <div class="form-group">
            <label for="logo">Upload New Logo</label>
            <input type="file" id="logo" name="logo" class="form-control">
            <small>Recommended dimensions: max width 300px, max height 80px. Allowed types: PNG, JPG, GIF, SVG.</small>
        </div>
        <button type="submit" name="upload_logo" class="btn btn-primary">Upload Logo</button>
    </form>
</div>

<div class="content-box" style="margin-top: 2rem;">
    <h2>Footer Links</h2>
    <form action="settings.php" method="POST" id="footer-links-form" class="admin-form" style="max-width:100%">
        <div id="footer-links-container">
            <?php
            $footer_links = $settings['footer_links'] ?? [];
            if (!empty($footer_links)):
                foreach ($footer_links as $index => $link): ?>
                    <div class="footer-link-item" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="footer_links[<?php echo $index; ?>][text]" placeholder="Link Text" class="form-control" value="<?php echo htmlspecialchars($link['text']); ?>">
                        <input type="text" name="footer_links[<?php echo $index; ?>][url]" placeholder="Link URL" class="form-control" value="<?php echo htmlspecialchars($link['url']); ?>">
                        <button type="button" class="btn btn-danger remove-link-btn">Remove</button>
                    </div>
                <?php endforeach;
            endif;
            ?>
        </div>
        <button type="button" id="add-footer-link" class="btn" style="background-color:#2ecc71; color:white;">+ Add Link</button>
        <hr style="margin: 2rem 0;">
        <button type="submit" name="update_footer_links" class="btn btn-primary">Save Footer Links</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../templates/admin/footer.php'; ?>

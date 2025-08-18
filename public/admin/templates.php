<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/models/setting.php';

$conn = db_connect();

// --- ACTION HANDLING ---
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $theme = $_GET['theme'] ?? '';
    $themes_dir = __DIR__ . '/../../themes';

    // --- Activate Theme ---
    if ($action === 'activate' && !empty($theme)) {
        if (is_dir($themes_dir . '/' . $theme)) {
            update_setting($conn, 'active_theme', $theme);
            $_SESSION['success_message'] = "Theme '" . htmlspecialchars($theme) . "' activated.";
        } else {
            $_SESSION['errors'][] = "Theme directory not found.";
        }
        header("Location: templates.php");
        exit();
    }

    // --- Delete Theme ---
    if ($action === 'delete' && !empty($theme)) {
        $active_theme_setting = get_all_settings($conn)['active_theme'] ?? 'default';
        if ($theme === 'default' || $theme === $active_theme_setting) {
            $_SESSION['errors'][] = 'Cannot delete the default or active theme.';
        } else {
            $theme_path = $themes_dir . '/' . $theme;
            if (is_dir($theme_path)) {
                // Simple recursive delete function
                function delete_dir($dir) {
                    if (!is_dir($dir)) return;
                    $files = array_diff(scandir($dir), ['.','..']);
                    foreach ($files as $file) {
                        (is_dir("$dir/$file")) ? delete_dir("$dir/$file") : unlink("$dir/$file");
                    }
                    rmdir($dir);
                }
                delete_dir($theme_path);
                $_SESSION['success_message'] = "Theme '" . htmlspecialchars($theme) . "' has been deleted.";
            } else {
                $_SESSION['errors'][] = "Theme directory not found.";
            }
        }
        header("Location: templates.php");
        exit();
    }
}

// --- Upload Theme ---
if (isset($_POST['upload_theme'])) {
    if (isset($_FILES['theme_zip']) && $_FILES['theme_zip']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['theme_zip'];
        if ($file['type'] === 'application/zip') {
            $zip = new ZipArchive;
            if ($zip->open($file['tmp_name']) === TRUE) {
                // Check for theme.json in the root of the zip
                if ($zip->locateName('theme.json') !== false) {
                    $zip->extractTo(__DIR__ . '/../../themes/');
                    $zip->close();
                    $_SESSION['success_message'] = 'Theme uploaded and installed successfully.';
                } else {
                     $_SESSION['errors'][] = 'Uploaded zip file is not a valid theme (missing theme.json).';
                }
            } else {
                $_SESSION['errors'][] = 'Failed to open the zip file.';
            }
        } else {
            $_SESSION['errors'][] = 'Invalid file type. Please upload a .zip file.';
        }
    } else {
        $_SESSION['errors'][] = 'An error occurred during file upload.';
    }
    header("Location: templates.php");
    exit();
}


// --- DATA FETCHING ---
$settings = get_all_settings($conn);
$settings = get_all_settings($conn);
$active_theme = $settings['active_theme'] ?? 'default';
$conn->close();

// --- THEME SCANNING ---
function scan_themes() {
    $themes_dir = __DIR__ . '/../../themes';
    $themes = [];
    if (!is_dir($themes_dir)) return [];

    $subdirectories = glob($themes_dir . '/*', GLOB_ONLYDIR);

    foreach ($subdirectories as $dir) {
        $theme_json_path = $dir . '/theme.json';
        if (file_exists($theme_json_path)) {
            $theme_data = json_decode(file_get_contents($theme_json_path), true);
            if ($theme_data) {
                $theme_id = basename($dir);
                $themes[$theme_id] = $theme_data;
                // Check for a screenshot
                if (file_exists($dir . '/screenshot.png')) {
                    $themes[$theme_id]['screenshot'] = '/themes/' . $theme_id . '/screenshot.png';
                } else {
                    // A placeholder image for themes without a screenshot
                    $themes[$theme_id]['screenshot'] = 'https://via.placeholder.com/400x300.png?text=No+Preview';
                }
            }
        }
    }
    return $themes;
}

$available_themes = scan_themes();
?>

<?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>

<h1>Theme Manager</h1>
<p>Manage the appearance of your public-facing website by activating or uploading new themes.</p>

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

<div class="themes-grid">
    <?php foreach ($available_themes as $id => $theme): ?>
        <div class="theme-card <?php if ($id === $active_theme) echo 'active'; ?>">
            <div class="theme-screenshot" style="background-image: url('<?php echo htmlspecialchars($theme['screenshot']); ?>');">
                <?php if ($id === $active_theme): ?>
                    <div class="active-badge">Active</div>
                <?php endif; ?>
            </div>
            <div class="theme-info">
                <h3><?php echo htmlspecialchars($theme['name']); ?>
                    <small>v<?php echo htmlspecialchars($theme['version']); ?></small>
                </h3>
                <p class="theme-author">by <?php echo htmlspecialchars($theme['author']); ?></p>
                <p class="theme-description"><?php echo htmlspecialchars($theme['description']); ?></p>
                <div class="theme-actions">
                    <?php if ($id !== $active_theme): ?>
                        <a href="templates.php?action=activate&theme=<?php echo urlencode($id); ?>" class="btn btn-primary">Activate</a>
                    <?php endif; ?>

                    <?php if ($id !== 'default'): ?>
                        <a href="templates.php?action=delete&theme=<?php echo urlencode($id); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this theme? This action cannot be undone.')">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="content-box" style="margin-top: 2rem;">
    <h2>Upload New Theme</h2>
    <form action="templates.php" method="POST" enctype="multipart/form-data" class="admin-form" style="max-width:100%">
        <div class="form-group">
            <label for="theme_zip">Theme .zip file</label>
            <input type="file" id="theme_zip" name="theme_zip" class="form-control" accept=".zip" required>
        </div>
        <button type="submit" name="upload_theme" class="btn btn-primary">Upload and Install Theme</button>
    </form>
</div>


<?php require_once __DIR__ . '/../../templates/admin/footer.php'; ?>

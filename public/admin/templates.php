<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../src/includes/csrf.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/models/setting.php';

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
                if (file_exists($dir . '/screenshot.png')) {
                    $themes[$theme_id]['screenshot'] = '/themes/' . $theme_id . '/screenshot.png';
                } else {
                    $themes[$theme_id]['screenshot'] = 'https://via.placeholder.com/400x300.png?text=No+Preview';
                }
            }
        }
    }
    return $themes;
}

function delete_theme_dir($dir_path) {
    if (!is_dir($dir_path)) return;
    $it = new RecursiveDirectoryIterator($dir_path, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isDir()) { rmdir($file->getRealPath()); } else { unlink($file->getRealPath()); }
    }
    rmdir($dir_path);
}

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_token();
    $response = ['status' => 'error', 'errors' => []];
    header('Content-Type: application/json');

    if (isset($_POST['activate_theme'])) {
        $theme_id = basename($_POST['theme_id']);
        if (is_dir(__DIR__ . '/../../themes/' . $theme_id)) {
            update_setting($conn, 'active_theme', $theme_id);
            $response = ['status' => 'success', 'message' => "Theme '{$theme_id}' activated."];
        } else {
            $response['errors'][] = 'Theme not found.';
        }
    }

    if (isset($_POST['delete_theme'])) {
        $theme_id = basename($_POST['theme_id']);
        $active_theme_setting = get_all_settings($conn)['active_theme'] ?? 'default';
        if ($theme_id !== 'default' && $theme_id !== $active_theme_setting) {
            $theme_path = __DIR__ . '/../../themes/' . $theme_id;
            if (is_dir($theme_path)) {
                delete_theme_dir($theme_path);
                $response = ['status' => 'success', 'message' => "Theme '{$theme_id}' deleted."];
            }
        } else {
            $response['errors'][] = 'Cannot delete the active or default theme.';
        }
    }

    if (isset($_POST['upload_theme'])) {
        if (isset($_FILES['theme_zip']) && $_FILES['theme_zip']['error'] === UPLOAD_ERR_OK) {
            $zip = new ZipArchive;
            if ($zip->open($_FILES['theme_zip']['tmp_name']) === TRUE) {
                $theme_name = str_replace('.zip', '', basename($_FILES['theme_zip']['name']));
                $install_path = __DIR__ . '/../../themes/' . $theme_name;
                if (!is_dir($install_path) && $zip->locateName('theme.json') !== false) {
                    $zip->extractTo($install_path);
                    $response = ['status' => 'success', 'message' => "Theme '{$theme_name}' uploaded."];
                } else {
                    $response['errors'][] = 'Theme already exists or zip is invalid.';
                }
                $zip->close();
            } else { $response['errors'][] = 'Failed to open zip.'; }
        } else { $response['errors'][] = 'File upload error.'; }
    }

    echo json_encode($response);
    exit();
}

$settings = get_all_settings($conn);
$active_theme = $settings['active_theme'] ?? 'default';
$available_themes = scan_themes();
$conn->close();
csrf_generate_token();
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
                        <form action="templates.php" method="POST" class="ajax-form" style="display:inline;">
                            <input type="hidden" name="theme_id" value="<?php echo htmlspecialchars($id); ?>">
                            <button type="submit" name="activate_theme" class="btn btn-primary">Activate</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($id !== 'default' && $id !== $active_theme): ?>
                         <form action="templates.php" method="POST" class="ajax-form" style="display:inline;">
                            <input type="hidden" name="theme_id" value="<?php echo htmlspecialchars($id); ?>">
                            <button type="submit" name="delete_theme" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="content-box" style="margin-top: 2rem;">
    <h2>Upload New Theme</h2>
    <form action="templates.php" method="POST" enctype="multipart/form-data" class="admin-form ajax-form" style="max-width:100%">
        <div class="form-group">
            <label for="theme_zip">Theme .zip file</label>
            <input type="file" id="theme_zip" name="theme_zip" class="form-control" accept=".zip" required>
        </div>
        <button type="submit" name="upload_theme" class="btn btn-primary">Upload and Install Theme</button>
    </form>
</div>


<?php require_once __DIR__ . '/../../templates/admin/footer.php'; ?>

<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../config/database.php';

// --- Logic to scan for plugins ---
function get_available_plugins() {
    $plugins_dir = __DIR__ . '/../../plugins';
    $plugins = [];
    if (!is_dir($plugins_dir)) return [];

    $directories = new DirectoryIterator($plugins_dir);
    foreach ($directories as $fileinfo) {
        if ($fileinfo->isDir() && !$fileinfo->isDot()) {
            $dir = $fileinfo->getPathname();
            $plugin_json_path = $dir . '/plugin.json';
            if (file_exists($plugin_json_path)) {
                $plugin_data = json_decode(file_get_contents($plugin_json_path), true);
                if ($plugin_data) {
                    $plugin_id = $fileinfo->getBasename();
                    $plugins[$plugin_id] = $plugin_data;
                }
            }
        }
    }
    return $plugins;
}

$available_plugins = get_available_plugins();

$conn = db_connect();

// --- Recursive Delete Function ---
function delete_plugin_dir($dir_path) {
    if (!is_dir($dir_path)) return;
    $it = new RecursiveDirectoryIterator($dir_path, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){ rmdir($file->getRealPath()); } else { unlink($file->getRealPath()); }
    }
    rmdir($dir_path);
}

// --- FORM PROCESSING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plugin_folder = basename($_POST['plugin_folder'] ?? '');

    // --- Activate Plugin ---
    if (isset($_POST['activate_plugin'])) {
        $plugin_json_path = __DIR__ . '/../../plugins/' . $plugin_folder . '/plugin.json';
        if (file_exists($plugin_json_path)) {
            $plugin_data = json_decode(file_get_contents($plugin_json_path), true);
            $stmt = $conn->prepare("INSERT INTO plugins (name, plugin_folder, description, version, is_active) VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE is_active = 1");
            $stmt->bind_param("ssss", $plugin_data['name'], $plugin_folder, $plugin_data['description'], $plugin_data['version']);
            $stmt->execute();
            $_SESSION['success_message'] = "Plugin '{$plugin_data['name']}' activated.";
        }
    }

    // --- Deactivate Plugin ---
    if (isset($_POST['deactivate_plugin'])) {
        $stmt = $conn->prepare("UPDATE plugins SET is_active = 0 WHERE plugin_folder = ?");
        $stmt->bind_param("s", $plugin_folder);
        $stmt->execute();
        $_SESSION['success_message'] = "Plugin '{$plugin_folder}' deactivated.";
    }

    // --- Delete Plugin ---
    if (isset($_POST['delete_plugin'])) {
        // First, delete from DB
        $stmt = $conn->prepare("DELETE FROM plugins WHERE plugin_folder = ?");
        $stmt->bind_param("s", $plugin_folder);
        $stmt->execute();
        // Then, delete files
        $plugin_path = __DIR__ . '/../../plugins/' . $plugin_folder;
        if (is_dir($plugin_path)) {
            delete_plugin_dir($plugin_path);
        }
        $_SESSION['success_message'] = "Plugin '{$plugin_folder}' deleted.";
    }

    // --- Upload Plugin ---
    if (isset($_POST['upload_plugin'])) {
        if (isset($_FILES['plugin_zip']) && $_FILES['plugin_zip']['error'] === UPLOAD_ERR_OK) {
            $zip_file = $_FILES['plugin_zip']['tmp_name'];
            $zip = new ZipArchive;
            if ($zip->open($zip_file) === TRUE) {
                $plugin_name = str_replace('.zip', '', basename($_FILES['plugin_zip']['name']));
                $install_path = __DIR__ . '/../../plugins/' . $plugin_name;

                if (!is_dir($install_path) && $zip->locateName('plugin.json') !== false) {
                    $zip->extractTo($install_path);
                    $zip->close();
                    $_SESSION['success_message'] = "Plugin '{$plugin_name}' uploaded successfully.";
                } else {
                    $zip->close();
                    $_SESSION['errors'][] = 'Plugin already exists or the zip is invalid (missing plugin.json).';
                }
            } else { $_SESSION['errors'][] = 'Failed to open zip file.'; }
        } else { $_SESSION['errors'][] = 'Error during file upload.'; }
    }

    header("Location: plugins.php");
    exit();
}

// Get active plugins from the database
$active_plugins_result = $conn->query("SELECT plugin_folder FROM plugins WHERE is_active = 1");
$active_plugins = array_column($active_plugins_result->fetch_all(MYSQLI_ASSOC), 'plugin_folder');
$conn->close();

?>
<?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>

<h1>Plugin Manager</h1>
<p>Activate, deactivate, or delete plugins. Active plugins will be loaded on every page.</p>

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
    <h3>Installed Plugins</h3>
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 25%;">Plugin</th>
                <th>Description</th>
                <th style="width: 25%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($available_plugins)): ?>
                 <tr>
                    <td colspan="3" style="text-align: center;">No plugins found. Upload one below.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($available_plugins as $id => $plugin): ?>
                    <?php $is_active = in_array($id, $active_plugins); ?>
                    <tr style="<?php if($is_active) echo 'background-color: #e8f5e9;'; ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($plugin['name']); ?></strong>
                            <br>
                            <small>v<?php echo htmlspecialchars($plugin['version']); ?> by <?php echo htmlspecialchars($plugin['author']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($plugin['description']); ?></td>
                        <td>
                            <?php if ($is_active): ?>
                                <form action="plugins.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="plugin_folder" value="<?php echo htmlspecialchars($id); ?>">
                                    <button type="submit" name="deactivate_plugin" class="btn" style="background-color:#f39c12;">Deactivate</button>
                                </form>
                            <?php else: ?>
                                 <form action="plugins.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="plugin_folder" value="<?php echo htmlspecialchars($id); ?>">
                                    <button type="submit" name="activate_plugin" class="btn btn-primary">Activate</button>
                                </form>
                            <?php endif; ?>

                            <?php if (!$is_active): // Can only delete inactive plugins ?>
                            <form action="plugins.php" method="POST" style="display:inline;">
                                <input type="hidden" name="plugin_folder" value="<?php echo htmlspecialchars($id); ?>">
                                <button type="submit" name="delete_plugin" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this plugin? This will delete its files.')">Delete</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="content-box" style="margin-top: 2rem;">
    <h2>Upload New Plugin</h2>
    <form action="plugins.php" method="POST" enctype="multipart/form-data" class="admin-form" style="max-width:100%">
        <div class="form-group">
            <label for="plugin_zip">Plugin .zip file</label>
            <input type="file" name="plugin_zip" id="plugin_zip" class="form-control" accept=".zip" required>
        </div>
        <button type="submit" name="upload_plugin" class="btn btn-primary">Upload and Install Plugin</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../templates/admin/footer.php'; ?>

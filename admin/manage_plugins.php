<?php
// Admin Page: Manage Plugins
session_start();

// --- Load core files and check user permissions ---
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/language.php';

$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'founder';
if (!$is_logged_in) {
    header('Location: index.php');
    exit;
}

// --- Plugin Management Logic ---
$feedback_message = '';
$prefix = DB_PREFIX;

// Handle activation/deactivation
if (isset($_GET['action']) && isset($_GET['plugin'])) {
    $action = $_GET['action'];
    $plugin_dir = basename($_GET['plugin']); // Sanitize input

    if ($action === 'activate') {
        $stmt = $mysqli->prepare("INSERT INTO `{$prefix}plugins` (directory_name, is_active) VALUES (?, 1) ON DUPLICATE KEY UPDATE is_active = 1");
        $stmt->bind_param('s', $plugin_dir);
        $stmt->execute();
        $stmt->close();
        $feedback_message = "Plugin '{$plugin_dir}' activated.";
    } elseif ($action === 'deactivate') {
        $stmt = $mysqli->prepare("INSERT INTO `{$prefix}plugins` (directory_name, is_active) VALUES (?, 0) ON DUPLICATE KEY UPDATE is_active = 0");
        $stmt->bind_param('s', $plugin_dir);
        $stmt->execute();
        $stmt->close();
        $feedback_message = "Plugin '{$plugin_dir}' deactivated.";
    }
    // Redirect to clean the URL
    header('Location: manage_plugins.php');
    exit;
}


// --- Discover Plugins ---
$plugins_dir = __DIR__ . '/../plugins/';
$discovered_plugins = [];
if (is_dir($plugins_dir)) {
    $plugin_folders = array_filter(glob($plugins_dir . '*'), 'is_dir');
    foreach ($plugin_folders as $folder) {
        $manifest_file = $folder . '/plugin.json';
        if (file_exists($manifest_file)) {
            $manifest_data = json_decode(file_get_contents($manifest_file), true);
            if ($manifest_data) {
                $manifest_data['directory'] = basename($folder);
                $discovered_plugins[basename($folder)] = $manifest_data;
            }
        }
    }
}

// --- Get Plugin Statuses from DB ---
$active_plugins_db = $mysqli->query("SELECT directory_name FROM `{$prefix}plugins` WHERE is_active = 1");
$active_plugins = [];
while($row = $active_plugins_db->fetch_assoc()) {
    $active_plugins[] = $row['directory_name'];
}


// --- Load the admin template ---
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page content starts here -->
<h1><?php echo t('manage_plugins_title', 'Manage Plugins'); ?></h1>
<p><?php echo t('manage_plugins_description', 'Here you can activate and deactivate plugins for your site.'); ?></p>

<?php if ($feedback_message): ?>
    <p><strong><?php echo htmlspecialchars($feedback_message); ?></strong></p>
<?php endif; ?>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background: #eee;">
            <th style="padding: 10px; text-align: left;"><?php echo t('plugin_name_header', 'Plugin'); ?></th>
            <th style="padding: 10px; text-align: left;"><?php echo t('plugin_description_header', 'Description'); ?></th>
            <th style="padding: 10px; text-align: left;"><?php echo t('plugin_actions_header', 'Actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($discovered_plugins as $dir => $plugin): ?>
        <tr style="<?php echo in_array($dir, $active_plugins) ? 'background-color: #e7f7e7;' : ''; ?>">
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                <strong><?php echo htmlspecialchars($plugin['name']); ?></strong><br>
                <small>v<?php echo htmlspecialchars($plugin['version']); ?> | By <?php echo htmlspecialchars($plugin['author']); ?></small>
            </td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($plugin['description']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                <?php if (in_array($dir, $active_plugins)): ?>
                    <a href="?action=deactivate&plugin=<?php echo urlencode($dir); ?>"><?php echo t('plugin_action_deactivate', 'Deactivate'); ?></a>
                <?php else: ?>
                    <a href="?action=activate&plugin=<?php echo urlencode($dir); ?>"><?php echo t('plugin_action_activate', 'Activate'); ?></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<?php
require_once __DIR__ . '/includes/footer.php';
?>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';
$page_title = 'Manage Themes';

$message = '';

// Handle theme activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_themes'])) {
    $active_template = $_POST['frontend_theme'];
    $admin_theme = $_POST['admin_theme'];

    $stmt1 = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'active_template'");
    $stmt1->bind_param("s", $active_template);
    $stmt1->execute();

    $stmt2 = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'admin_theme'");
    $stmt2->bind_param("s", $admin_theme);
    $stmt2->execute();

    $message = "Themes updated successfully!";
}


// Fetch current settings
$settings_result = $conn->query("SELECT * FROM settings");
$settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$active_template = $settings['active_template'];
$admin_theme = $settings['admin_theme'];


// Scan for available frontend templates
$frontend_template_dirs = glob('../templates/*', GLOB_ONLYDIR);
$available_frontend_templates = array_map('basename', $frontend_template_dirs);

// Scan for available admin themes
$admin_theme_files = glob('../assets/css/admin_themes/*.css');
$available_admin_themes = array_map('basename', $admin_theme_files);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Themes</title>
    <link rel="stylesheet" href="../assets/css/admin_themes/<?php echo $admin_theme; ?>">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <?php if ($message): ?>
                    <p class="message success"><?php echo $message; ?></p>
                <?php endif; ?>

                <div class="card">
                    <h3>Activate Themes</h3>
                    <form action="themes.php" method="post">
                        <div class="input-group">
                            <label for="frontend_theme">Frontend Theme</label>
                            <select name="frontend_theme" id="frontend_theme">
                                <?php foreach ($available_frontend_templates as $template): ?>
                                    <option value="<?php echo $template; ?>" <?php echo ($template === $active_template) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(ucfirst($template)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="admin_theme">Admin Panel Theme</label>
                            <select name="admin_theme" id="admin_theme">
                                <?php foreach ($available_admin_themes as $theme): ?>
                                    <option value="<?php echo $theme; ?>" <?php echo ($theme === $admin_theme) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(ucfirst(str_replace('.css', '', $theme))); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="save_themes">Save Themes</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

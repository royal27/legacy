<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';

$message = '';

// Handle activating a template
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate_template'])) {
    $template_name = $_POST['template_name'];
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'active_template'");
    $stmt->bind_param("s", $template_name);
    if ($stmt->execute()) {
        $message = "Template activated successfully!";
    } else {
        $message = "Error activating template.";
    }
}

// Handle uploading a template
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['template_zip'])) {
    $file = $_FILES['template_zip'];
    if ($file['error'] === UPLOAD_ERR_OK && $file['type'] === 'application/zip') {
        $target_dir = "../templates/";
        $zip = new ZipArchive;
        if ($zip->open($file['tmp_name']) === TRUE) {
            $zip->extractTo($target_dir);
            $zip->close();
            $message = "Template uploaded and extracted successfully!";
        } else {
            $message = "Failed to extract the zip file.";
        }
    } else {
        $message = "Invalid file. Please upload a ZIP file.";
    }
}


// Fetch current active template
$active_template_result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'active_template'");
$active_template = $active_template_result->fetch_assoc()['setting_value'];

// Scan for available templates
$template_dirs = glob('../templates/*', GLOB_ONLYDIR);
$available_templates = array_map('basename', $template_dirs);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Templates</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header>
            <h2>Manage Templates</h2>
        </header>
        <main>
            <?php if ($message): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>

            <div class="card">
                <h3>Installed Templates</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Template Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($available_templates as $template): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($template); ?></td>
                                <td>
                                    <?php if ($template === $active_template): ?>
                                        <span style="color: green; font-weight: bold;">Active</span>
                                    <?php else: ?>
                                        Inactive
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($template !== $active_template): ?>
                                        <form action="templates.php" method="post" style="display: inline;">
                                            <input type="hidden" name="template_name" value="<?php echo $template; ?>">
                                            <button type="submit" name="activate_template" class="btn btn-primary">Activate</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3>Upload New Template</h3>
                <form action="templates.php" method="post" enctype="multipart/form-data">
                    <div class="input-group">
                        <label for="template_zip">Upload a .zip file</label>
                        <input type="file" name="template_zip" id="template_zip" accept=".zip" required>
                    </div>
                    <button type="submit">Upload Template</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

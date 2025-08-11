<?php
require_once 'admin_header_logic.php';
$page_title = 'Site Settings';

// Fetch current settings
$settings = [];
$result = $conn->query("SELECT * FROM settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

    try {
        // Update logo text
        if (isset($_POST['logo_text'])) {
            $logo_text = $_POST['logo_text'];
            $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'logo_text'");
            $stmt->bind_param("s", $logo_text);
            $stmt->execute();
        }

        // Update footer text
        if (isset($_POST['footer_text'])) {
            $footer_text = $_POST['footer_text'];
            $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'footer_text'");
            $stmt->bind_param("s", $footer_text);
            $stmt->execute();
        }

        // Update invitations setting
        if (isset($_POST['enable_invitations'])) {
            $enable_invitations = $_POST['enable_invitations'];
            $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'enable_invitations'");
            $stmt->bind_param("s", $enable_invitations);
            $stmt->execute();
        }

        // Handle logo upload
        if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
            $image_name = time() . '_' . basename($_FILES['logo_image']['name']);
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . $image_name;
            if (move_uploaded_file($_FILES['logo_image']['tmp_name'], $target_file)) {
                if (!empty($settings['logo_image']) && file_exists($target_dir . $settings['logo_image'])) {
                    unlink($target_dir . $settings['logo_image']);
                }
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'logo_image'");
                $stmt->bind_param("s", $image_name);
                $stmt->execute();
            }
        }

        // Handle deleting the logo
        if (isset($_POST['delete_logo_image'])) {
            $target_dir = "../uploads/";
            if (!empty($settings['logo_image']) && file_exists($target_dir . $settings['logo_image'])) {
                unlink($target_dir . $settings['logo_image']);
            }
            $stmt = $conn->prepare("UPDATE settings SET setting_value = '' WHERE setting_key = 'logo_image'");
            $stmt->execute();
        }

        $response = ['status' => 'success', 'message' => 'Settings saved successfully!'];
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang_ui['site_settings']; ?></title>
    <link rel="stylesheet" href="../assets/css/admin_themes/<?php echo $admin_theme; ?>">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <form action="settings.php" method="post" enctype="multipart/form-data">
                    <div class="input-group">
                    <label for="logo_text"><?php echo $lang_ui['logo_text']; ?></label>
                    <input type="text" name="logo_text" id="logo_text" value="<?php echo htmlspecialchars($settings['logo_text']); ?>">
                </div>
                <div class="input-group">
                    <label for="logo_image"><?php echo $lang_ui['logo_image']; ?></label>
                    <input type="file" name="logo_image" id="logo_image">
                    <?php if (!empty($settings['logo_image'])): ?>
                        <p>Current logo: <img src="../uploads/<?php echo htmlspecialchars($settings['logo_image']); ?>" alt="Current Logo" width="150"></p>
                        <button type="submit" name="delete_logo_image" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete the logo image?');"><?php echo $lang_ui['delete']; ?> Logo</button>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <label for="footer_text"><?php echo $lang_ui['footer_text']; ?></label>
                    <input type="text" name="footer_text" id="footer_text" value="<?php echo htmlspecialchars($settings['footer_text']); ?>" required>
                </div>
                <hr>
                <div class="input-group">
                    <label><?php echo $lang_ui['enable_invitations']; ?></label>
                    <label><input type="radio" name="enable_invitations" value="1" <?php echo ($settings['enable_invitations'] == 1) ? 'checked' : ''; ?>> <?php echo $lang_ui['yes']; ?></label>
                    <label><input type="radio" name="enable_invitations" value="0" <?php echo ($settings['enable_invitations'] == 0) ? 'checked' : ''; ?>> <?php echo $lang_ui['no']; ?></label>
                </div>
                <button type="submit" name="save_settings"><?php echo $lang_ui['save']; ?> Settings</button>
            </form>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

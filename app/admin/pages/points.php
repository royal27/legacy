<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

// Security Check
if (!user_has_permission('manage_settings')) {
    echo '<div class="message-box error">You do not have permission to manage the points system.</div>';
    return;
}

$message = '';
$message_type = '';

// --- Handle form submission for updating settings ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_points_settings') {
    validate_csrf_token();
    $points_on_register = (int)$_POST['points_on_register'];
    $points_on_login = (int)$_POST['points_on_login'];

    $settings_to_update = [
        'points_on_register' => $points_on_register,
        'points_on_login' => $points_on_login,
    ];

    $db->begin_transaction();
    try {
        $stmt = $db->prepare("INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");

        foreach ($settings_to_update as $name => $value) {
            $stmt->bind_param('ss', $name, $value);
            $stmt->execute();
        }
        $stmt->close();
        $db->commit();
        $message = 'Points settings updated successfully.';
        $message_type = 'success';

        // Refresh settings global variable
        $settings_res = $db->query("SELECT name, value FROM settings");
        if ($settings_res) {
            while($row = $settings_res->fetch_assoc()) {
                $settings[$row['name']] = $row['value'];
            }
        }

    } catch (mysqli_sql_exception $exception) {
        $db->rollback();
        $message = 'Error updating settings: ' . $exception->getMessage();
        $message_type = 'error';
    }
}

?>

<?php if ($message): ?>
<div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="content-block">
    <h2>Points System Settings</h2>
    <p>Configure the number of points awarded for user actions.</p>
    <form action="index.php?page=points" method="post">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="update_points_settings">

        <div class="form-group">
            <label for="points_on_register">Points for New Registration</label>
            <input type="number" id="points_on_register" name="points_on_register" value="<?php echo htmlspecialchars($settings['points_on_register'] ?? '50'); ?>" min="0">
            <small>Number of points a user receives when they create an account.</small>
        </div>

        <div class="form-group">
            <label for="points_on_login">Points for Daily Login</label>
            <input type="number" id="points_on_login" name="points_on_login" value="<?php echo htmlspecialchars($settings['points_on_login'] ?? '10'); ?>" min="0">
            <small>Number of points a user receives for their first login of the day.</small>
        </div>

        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>

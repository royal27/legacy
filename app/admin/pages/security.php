<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

// Use a high-level permission for this
if (!user_has_permission('manage_settings')) {
    echo '<div class="message-box error">You do not have permission to manage security settings.</div>';
    return;
}

$message = '';
$message_type = '';

// --- Handle POST Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token();
    // --- Add a new IP ban ---
    if (isset($_POST['action']) && $_POST['action'] === 'ban_ip') {
        $ip_address = trim($_POST['ip_address']);
        $reason = trim($_POST['reason']);

        if (filter_var($ip_address, FILTER_VALIDATE_IP)) {
            $stmt = $db->prepare("INSERT INTO banned_ips (ip_address, reason) VALUES (?, ?)");
            $stmt->bind_param('ss', $ip_address, $reason);
            if ($stmt->execute()) {
                $message = 'IP address banned successfully.';
                $message_type = 'success';
            } else {
                $message = 'Error banning IP. It might already be banned.';
                $message_type = 'error';
            }
            $stmt->close();
        } else {
            $message = 'Invalid IP address format.';
            $message_type = 'error';
        }
    }
    // --- Unban an IP ---
    if (isset($_POST['action']) && $_POST['action'] === 'unban_ip') {
        $ban_id = (int)$_POST['ban_id'];
        $stmt = $db->prepare("DELETE FROM banned_ips WHERE id = ?");
        $stmt->bind_param('i', $ban_id);
        if ($stmt->execute()) {
            $message = 'IP address unbanned successfully.';
            $message_type = 'success';
        } else {
            $message = 'Error unbanning IP.';
            $message_type = 'error';
        }
    }
}


// --- Fetch Data for Display ---
$banned_ips = $db->query("SELECT * FROM banned_ips ORDER BY banned_at DESC")->fetch_all(MYSQLI_ASSOC);

?>

<?php if ($message): ?>
<div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="content-block">
    <h2>Manage Banned IP Addresses</h2>
    <p>IP addresses on this list will be completely blocked from accessing the site.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Reason</th>
                <th>Banned At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($banned_ips as $ban): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($ban['ip_address']); ?></strong></td>
                <td><?php echo htmlspecialchars($ban['reason']); ?></td>
                <td><?php echo date('F j, Y, g:i a', strtotime($ban['banned_at'])); ?></td>
                <td>
                    <form action="index.php?page=security" method="post" style="display:inline;">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="unban_ip">
                        <input type="hidden" name="ban_id" value="<?php echo $ban['id']; ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Unban</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
             <?php if (empty($banned_ips)): ?>
                <tr><td colspan="4">No IP addresses are currently banned.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="content-block">
    <h2>Ban a New IP Address</h2>
    <form action="index.php?page=security" method="post">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="ban_ip">
        <div class="form-group">
            <label for="ip_address">IP Address</label>
            <input type="text" id="ip_address" name="ip_address" required>
        </div>
        <div class="form-group">
            <label for="reason">Reason (optional)</label>
            <input type="text" id="reason" name="reason">
        </div>
        <button type="submit" class="btn btn-accent">Add Ban</button>
    </form>
</div>

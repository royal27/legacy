<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

// For now, we'll use manage_settings permission. Later, a dedicated 'manage_menus' could be added.
if (!user_has_permission('manage_settings')) {
    echo '<div class="message-box error">You do not have permission to manage menus.</div>';
    return;
}

$message = '';
$message_type = '';

$available_locations = ['main_nav' => 'Main Navigation', 'footer_nav' => 'Footer Navigation'];
$menu_location = $_GET['location'] ?? 'main_nav';
if (!array_key_exists($menu_location, $available_locations)) {
    $menu_location = 'main_nav'; // Default to main_nav if invalid location
}


// --- Handle POST Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] !== 'update_order') {
        validate_csrf_token();
    }
    // Add/Edit Menu Item
    if (isset($_POST['action']) && $_POST['action'] === 'save_item') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title']);
        $url = trim($_POST['url']);
        $target = $_POST['target'];
        $permission = trim($_POST['permission_required']);
        $location = $_POST['menu_location'] ?? 'main_nav';

        if (!empty($title) && !empty($url)) {
            if ($id > 0) { // Update
                $stmt = $db->prepare("UPDATE menu_items SET title = ?, url = ?, target = ?, permission_required = ? WHERE id = ?");
                $stmt->bind_param('ssssi', $title, $url, $target, $permission, $id);
                $message = 'Menu item updated.';
            } else { // Insert
                $stmt = $db->prepare("INSERT INTO menu_items (title, url, target, menu_location, permission_required) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssss', $title, $url, $target, $location, $permission);
                $message = 'Menu item added.';
            }
            if ($stmt->execute()) {
                $message_type = 'success';
            } else {
                $message = 'Error saving menu item.';
                $message_type = 'error';
            }
            $stmt->close();
        } else {
            $message = 'Title and URL are required.';
            $message_type = 'error';
        }
    }
    // Delete Menu Item
    if (isset($_POST['action']) && $_POST['action'] === 'delete_item') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $message = 'Menu item deleted.';
            $message_type = 'success';
        } else {
            $message = 'Error deleting item.';
            $message_type = 'error';
        }
    }
}

// --- Handle AJAX for sorting ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order') {
    $order = $_POST['order']; // array of item IDs
    $i = 1;
    $stmt = $db->prepare("UPDATE menu_items SET sort_order = ? WHERE id = ?");
    foreach ($order as $item_id) {
        $stmt->bind_param('ii', $i, $item_id);
        $stmt->execute();
        $i++;
    }
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Menu order saved!']);
    exit;
}


// --- Fetch data for display ---
$menu_items = $db->query("SELECT * FROM menu_items WHERE menu_location = '{$menu_location}' ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC);
?>

<?php if ($message): ?>
<div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="nav-tabs">
    <?php foreach ($available_locations as $loc_key => $loc_name): ?>
        <a href="index.php?page=menus&location=<?php echo $loc_key; ?>" class="<?php echo ($menu_location === $loc_key) ? 'active' : ''; ?>">
            <?php echo $loc_name; ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="content-block">
    <h2>Manage <?php echo $available_locations[$menu_location]; ?></h2>
    <p>Drag and drop the rows to reorder the menu items.</p>
    <table class="data-table">
        <tbody id="sortable-menu">
            <?php foreach ($menu_items as $item): ?>
            <tr data-id="<?php echo $item['id']; ?>">
                <td>
                    <form action="index.php?page=menus&location=<?php echo $menu_location; ?>" method="post" class="menu-edit-form">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="save_item">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <input type="text" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" placeholder="Title">
                        <input type="text" name="url" value="<?php echo htmlspecialchars($item['url']); ?>" placeholder="URL">
                        <input type="text" name="permission_required" value="<?php echo htmlspecialchars($item['permission_required'] ?? ''); ?>" placeholder="Permission (optional)">
                        <select name="target">
                            <option value="_self" <?php echo ($item['target'] == '_self') ? 'selected' : ''; ?>>Same Tab</option>
                            <option value="_blank" <?php echo ($item['target'] == '_blank') ? 'selected' : ''; ?>>New Tab</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </form>
                    <form action="index.php?page=menus&location=<?php echo $menu_location; ?>" method="post" style="display:inline-block; margin-left: 5px;">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="delete_item">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <button type="submit" class="btn btn-accent btn-sm" onclick="return confirm('Are you sure you want to delete this menu item?');">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="content-block">
    <h2>Add New Menu Item to <?php echo $available_locations[$menu_location]; ?></h2>
    <form action="index.php?page=menus&location=<?php echo $menu_location; ?>" method="post">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="save_item">
        <input type="hidden" name="menu_location" value="<?php echo $menu_location; ?>">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="url">URL</label>
            <input type="text" id="url" name="url" required>
        </div>
        <div class="form-group">
            <label for="permission_required">Permission Required (Optional)</label>
            <input type="text" id="permission_required" name="permission_required">
            <small>Enter the name of a permission to restrict this menu item to roles with that permission.</small>
        </div>
        <div class="form-group">
            <label for="target">Target</label>
            <select id="target" name="target">
                <option value="_self">Same Tab</option>
                <option value="_blank">New Tab</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Add Item</button>
    </form>
</div>

<script>
$(document).ready(function() {
    // Make table rows sortable
    $('#sortable-menu').sortable({
        update: function(event, ui) {
            var order = $(this).sortable('toArray', { attribute: 'data-id' });
            $.ajax({
                url: window.location.href, // Post to the current URL
                type: 'POST',
                dataType: 'json',
                data: { action: 'update_order', order: order },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                    } else {
                        toastr.error('Failed to save order.');
                    }
                }
            });
        }
    }).disableSelection();
});
</script>

<style>
#sortable-menu tr { cursor: move; }
.menu-edit-form { display: flex; gap: 10px; align-items: center; }
.menu-edit-form input, .menu-edit-form select { flex-grow: 1; }
</style>

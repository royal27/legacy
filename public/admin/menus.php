<?php
require_once __DIR__ . '/../../src/includes/admin_check.php';
require_once __DIR__ . '/../../src/includes/csrf.php';
require_once __DIR__ . '/../../config/database.php';

$conn = db_connect();

// --- ACTION & FORM HANDLING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_token();
    $response = ['status' => 'error', 'errors' => []];
    header('Content-Type: application/json');

    if (isset($_POST['save_menu_item'])) {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name']);
        $link = trim($_POST['link']);
        $parent_id = (int)$_POST['parent_id'];
        $menu_type = $_POST['menu_type'];
        $display_order = (int)$_POST['display_order'];
        $permissions = $_POST['permissions'] ?? [];

        $conn->begin_transaction();
        try {
            if ($id > 0) {
                $stmt = $conn->prepare("UPDATE menus SET name=?, link=?, parent_id=?, menu_type=?, display_order=? WHERE id=?");
                $stmt->bind_param("ssisii", $name, $link, $parent_id, $menu_type, $display_order, $id);
                $stmt->execute();
                $menu_id = $id;
            } else {
                $stmt = $conn->prepare("INSERT INTO menus (name, link, parent_id, menu_type, display_order) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisi", $name, $link, $parent_id, $menu_type, $display_order);
                $stmt->execute();
                $menu_id = $conn->insert_id;
            }
            $stmt->close();

            $delete_perms_stmt = $conn->prepare("DELETE FROM menu_permissions WHERE menu_id = ?");
            $delete_perms_stmt->bind_param("i", $menu_id);
            $delete_perms_stmt->execute();
            $delete_perms_stmt->close();

            if (!empty($permissions)) {
                $insert_perms_stmt = $conn->prepare("INSERT INTO menu_permissions (menu_id, permission_id) VALUES (?, ?)");
                foreach ($permissions as $permission_id) {
                    $insert_perms_stmt->bind_param("ii", $menu_id, $permission_id);
                    $insert_perms_stmt->execute();
                }
                $insert_perms_stmt->close();
            }

            $conn->commit();
            $response = ['status' => 'success', 'message' => 'Menu item saved successfully.'];
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $response['errors'][] = 'Failed to save menu item.';
        }
    }

    if (isset($_POST['delete_menu_item'])) {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM menus WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            $response = ['status' => 'success', 'message' => 'Menu item deleted.'];
        } else {
            $response['errors'][] = 'Failed to delete menu item.';
        }
        $stmt->close();
    }

    echo json_encode($response);
    exit();
}

// --- DATA FETCHING ---
$all_menus_flat = $conn->query("SELECT * FROM menus ORDER BY menu_type, display_order ASC, name ASC")->fetch_all(MYSQLI_ASSOC);
$permissions = $conn->query("SELECT * FROM permissions ORDER BY permission_name ASC")->fetch_all(MYSQLI_ASSOC);
csrf_generate_token();

// Fetch permissions for each menu item
$menu_permissions_map = [];
$result = $conn->query("SELECT menu_id, permission_id FROM menu_permissions");
while ($row = $result->fetch_assoc()) {
    $menu_permissions_map[$row['menu_id']][] = $row['permission_id'];
}


$menus_by_type = ['public' => [], 'admin' => [], 'dashboard' => []];
foreach ($all_menus_flat as $item) {
    $item['permissions'] = $menu_permissions_map[$item['id']] ?? [];
    $menus_by_type[$item['menu_type']][] = $item;
}


// Recursive function to display menu items in the admin list
function build_menu_tree(array &$elements, $parentId = 0) {
    $branch = [];
    foreach ($elements as $key => $element) {
        if ($element['parent_id'] == $parentId) {
            $children = build_menu_tree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
            unset($elements[$key]);
        }
    }
    return $branch;
}

function display_admin_menu_list(array $menu_items) {
    echo '<ol class="menu-list">';
    foreach ($menu_items as $item) {
        $data_attributes = "data-id='{$item['id']}' ";
        $data_attributes .= "data-name='" . htmlspecialchars($item['name']) . "' ";
        $data_attributes .= "data-link='" . htmlspecialchars($item['link']) . "' ";
        $data_attributes .= "data-parent_id='{$item['parent_id']}' ";
        $data_attributes .= "data-menu_type='{$item['menu_type']}' ";
        $data_attributes .= "data-display_order='{$item['display_order']}' ";
        $data_attributes .= "data-permissions='" . json_encode($item['permissions']) . "'";

        echo '<li class="menu-list-item">';
        echo '<div class="menu-item-info">';
        echo '<span><strong>' . htmlspecialchars($item['name']) . '</strong> <small>(' . htmlspecialchars($item['link']) . ')</small></span>';
        echo '<div class="menu-item-actions">';
        echo '<button type="button" class="btn copy-link-btn" data-link="' . htmlspecialchars($item['link']) . '">Copy</button>';
        echo '<button type="button" class="btn btn-primary edit-menu-btn" ' . $data_attributes . '>Edit</button>';
        echo '<form action="menus.php" method="POST" class="ajax-form" style="display:inline; margin-left: 5px;">';
        echo csrf_input();
        echo '<input type="hidden" name="id" value="' . $item['id'] . '">';
        echo '<button type="submit" name="delete_menu_item" class="btn btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';

        if (!empty($item['children'])) {
            display_admin_menu_list($item['children']);
        }
        echo '</li>';
    }
    echo '</ol>';
}

$public_menu_tree = build_menu_tree($menus_by_type['public']);
$admin_menu_tree = build_menu_tree($menus_by_type['admin']);
$dashboard_menu_tree = build_menu_tree($menus_by_type['dashboard']);

$conn->close();
?>

<?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>

<h1>Menu Manager</h1>
<p>Manage all navigation menus across the site. Click "Edit" on an item to load it into the form below.</p>

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
    <h3 id="menu-form-title">Add New Menu Item</h3>
    <form id="menu-item-form" action="menus.php" method="POST" class="admin-form ajax-form" style="max-width:100%">
        <?php echo csrf_input(); ?>
        <input type="hidden" name="id" id="menu-id" value="0">
        <div class="form-group">
            <label for="name">Name / Label</label>
            <input type="text" id="menu-name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="link">Link (URL)</label>
            <input type="text" id="menu-link" name="link" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="menu_type">Menu Type</label>
            <select id="menu-type" name="menu_type" class="form-control">
                <option value="public">Public Menu</option>
                <option value="admin">Admin Sidebar</option>
                <option value="dashboard">User Dashboard</option>
            </select>
        </div>
         <div class="form-group">
            <label for="parent_id">Parent Item</label>
            <select id="menu-parent-id" name="parent_id" class="form-control">
                <option value="0">-- None (Root Level) --</option>
                <?php foreach($all_menus_flat as $item): ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo str_repeat('&nbsp;', 2) . htmlspecialchars($item['name']); ?> (<?php echo $item['menu_type']; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input type="number" id="menu-display-order" name="display_order" class="form-control" value="0">
        </div>
        <div class="form-group">
            <label for="permissions">Required Permissions (optional)</label>
            <small>If no permissions are selected, the link will be visible to everyone (who can see that menu type).</small>
            <select id="menu-permissions" name="permissions[]" class="form-control" multiple style="height: 150px;">
                <?php foreach($permissions as $permission): ?>
                    <option value="<?php echo $permission['id']; ?>"><?php echo htmlspecialchars($permission['permission_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="save_menu_item" class="btn btn-primary">Save Menu Item</button>
        <button type="button" id="clear-menu-form" class="btn" style="background-color: #7f8c8d; color:white;">Clear Form</button>
    </form>
</div>

<div class="content-box" style="margin-top: 2rem;">
    <h2>Existing Menus</h2>

    <h3>Public Menu</h3>
    <?php if (empty($public_menu_tree)) echo '<p>No items in this menu yet.</p>'; else display_admin_menu_list($public_menu_tree); ?>

    <hr style="margin: 2rem 0">

    <h3>Admin Sidebar</h3>
    <?php if (empty($admin_menu_tree)) echo '<p>No items in this menu yet.</p>'; else display_admin_menu_list($admin_menu_tree); ?>

    <hr style="margin: 2rem 0">

    <h3>User Dashboard Menu</h3>
    <?php if (empty($dashboard_menu_tree)) echo '<p>No items in this menu yet.</p>'; else display_admin_menu_list($dashboard_menu_tree); ?>
</div>

<?php require_once __DIR__ . '/../../templates/admin/footer.php'; ?>

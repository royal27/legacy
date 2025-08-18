<?php
// src/models/menu.php
require_once __DIR__ . '/user.php'; // For user_has_permission

/**
 * Fetches and builds a hierarchical menu for a specific user, respecting permissions.
 *
 * @param mysqli $conn
 * @param string $menu_type ('public', 'admin', 'dashboard')
 * @param int|null $user_id The user's ID, or null for guests.
 * @return array The menu tree.
 */
function get_menu_for_user(mysqli $conn, string $menu_type, ?int $user_id): array {
    // 1. Fetch all menu items of the specified type
    $sql = "SELECT m.id, m.name, m.link, m.parent_id,
                   GROUP_CONCAT(mp.permission_id) as required_permission_ids
            FROM menus m
            LEFT JOIN menu_permissions mp ON m.id = mp.menu_id
            WHERE m.menu_type = ?
            GROUP BY m.id
            ORDER BY m.display_order ASC, m.name ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $menu_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $all_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // 2. Fetch all permissions for the current user in one go
    $user_permissions = [];
    if ($user_id) {
        $perm_sql = "SELECT p.permission_name
                     FROM user_roles ur
                     JOIN role_permissions rp ON ur.role_id = rp.role_id
                     JOIN permissions p ON rp.permission_id = p.id
                     WHERE ur.user_id = ?";
        $perm_stmt = $conn->prepare($perm_sql);
        $perm_stmt->bind_param("i", $user_id);
        $perm_stmt->execute();
        $perm_result = $perm_stmt->get_result();
        while($row = $perm_result->fetch_assoc()) {
            $user_permissions[$row['permission_name']] = true;
        }
        $perm_stmt->close();
    }

    // 3. Filter items based on permissions
    $accessible_items = [];
    foreach ($all_items as $item) {
        if (empty($item['required_permission_ids'])) {
            // No permissions required, accessible to all (within its menu type)
            $accessible_items[] = $item;
            continue;
        }

        if (!$user_id) {
            // Has permissions, but user is a guest
            continue;
        }

        // Check if user has at least one of the required permissions
        $required_ids = explode(',', $item['required_permission_ids']);
        // This part is tricky without another DB query. A better approach is to check by name.
        // For now, we'll re-query. This is inefficient but works.
        // A better long-term solution would be to load all permission names and IDs into an array.
        $has_permission = false;
        foreach($required_ids as $pid) {
            $p_stmt = $conn->prepare("SELECT permission_name FROM permissions WHERE id = ?");
            $p_stmt->bind_param("i", $pid);
            $p_stmt->execute();
            $p_name = $p_stmt->get_result()->fetch_assoc()['permission_name'];
            $p_stmt->close();
            if (isset($user_permissions[$p_name])) {
                $has_permission = true;
                break;
            }
        }

        if ($has_permission) {
            $accessible_items[] = $item;
        }
    }

    // 4. Build the tree from the accessible items
    // This is a standard algorithm for building a tree from a flat array.
    $tree = [];
    $items_by_id = [];
    foreach ($accessible_items as $item) {
        $items_by_id[$item['id']] = $item;
        $items_by_id[$item['id']]['children'] = [];
    }

    foreach ($items_by_id as $id => &$item) {
        if ($item['parent_id'] && isset($items_by_id[$item['parent_id']])) {
            $items_by_id[$item['parent_id']]['children'][] = &$item;
        }
    }

    foreach ($items_by_id as $id => &$item) {
        if ($item['parent_id'] == 0) {
            $tree[] = &$item;
        }
    }

    return $tree;
}
?>

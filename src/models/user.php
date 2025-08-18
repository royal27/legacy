<?php
// src/models/user.php

/**
 * Checks if a user has a specific role.
 *
 * @param mysqli $conn The database connection object.
 * @param int $user_id The ID of the user to check.
 * @param string $role_name The name of the role to check for.
 * @return bool True if the user has the role, false otherwise.
 */
function user_has_role(mysqli $conn, int $user_id, string $role_name): bool {
    $sql = "SELECT COUNT(*)
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ? AND r.role_name = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // In a real app, you would log this error.
        error_log('Prepare failed: ' . $conn->error);
        return false;
    }

    $stmt->bind_param("is", $user_id, $role_name);
    $stmt->execute();

    $count = 0;
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count > 0;
}

/**
 * Gets all roles for a specific user.
 *
 * @param mysqli $conn The database connection object.
 * @param int $user_id The ID of the user.
 * @return array An array of role names.
 */
function get_user_roles(mysqli $conn, int $user_id): array {
    $roles = [];
    $sql = "SELECT r.role_name
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('Prepare failed: ' . $conn->error);
        return [];
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['role_name'];
    }

    $stmt->close();
    return $roles;
}

/**
 * Checks if a user has a specific permission through any of their assigned roles.
 *
 * @param mysqli $conn The database connection object.
 * @param int $user_id The ID of the user to check.
 * @param string $permission_name The name of the permission to check for (e.g., 'manage_settings').
 * @return bool True if the user has the permission, false otherwise.
 */
function user_has_permission(mysqli $conn, int $user_id, string $permission_name): bool {
    $sql = "SELECT 1
            FROM user_roles ur
            JOIN role_permissions rp ON ur.role_id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.id
            WHERE ur.user_id = ? AND p.permission_name = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('Prepare failed in user_has_permission: ' . $conn->error);
        return false;
    }

    $stmt->bind_param("is", $user_id, $permission_name);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->close();

    return $count > 0;
}
?>

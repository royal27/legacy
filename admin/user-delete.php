<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id_to_delete = $_GET['id'];

// Prevent the user from deleting themselves
if ($user_id_to_delete == $_SESSION['user_id']) {
    header("Location: users.php?error=Cannot delete yourself");
    exit();
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id_to_delete);

if ($stmt->execute()) {
    header("Location: users.php?success=User deleted");
} else {
    header("Location: users.php?error=Failed to delete user");
}
exit();
?>

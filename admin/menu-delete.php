<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: menus.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: menus.php");
    exit();
}

$id = $_GET['id'];

// First, get the image filename to delete it from the server
$stmt = $conn->prepare("SELECT image FROM menus WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $menu = $result->fetch_assoc();
    $image_to_delete = $menu['image'];

    // Delete the menu item from the database
    $delete_stmt = $conn->prepare("DELETE FROM menus WHERE id = ?");
    $delete_stmt->bind_param("i", $id);
    if ($delete_stmt->execute()) {
        // If deletion is successful, delete the image file
        if ($image_to_delete && file_exists('../uploads/' . $image_to_delete)) {
            unlink('../uploads/' . $image_to_delete);
        }
    }
    $delete_stmt->close();
}
$stmt->close();
$conn->close();

header("Location: menus.php");
exit();
?>

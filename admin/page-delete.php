<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pages.php");
    exit();
}

$page_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM pages WHERE id = ?");
$stmt->bind_param("i", $page_id);

if ($stmt->execute()) {
    header("Location: pages.php?success=1");
} else {
    header("Location: pages.php?error=1");
}
exit();
?>

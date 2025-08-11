<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!</h2>
        </header>
        <main>
            <p>Welcome to the admin panel. Please select a section from the sidebar to get started.</p>
            <p>Your role is: <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong></p>
        </main>
    </div>
</body>
</html>

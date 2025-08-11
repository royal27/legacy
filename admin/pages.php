<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';
$page_title = 'Manage Pages';

// Fetch all pages
$pages = $conn->query("SELECT id, title, slug, show_in_footer FROM pages ORDER BY title");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pages</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <a href="page-edit.php" class="btn btn-add">Add New Page</a>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>In Footer?</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($page = $pages->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($page['title']); ?></td>
                                <td>/<?php echo htmlspecialchars($page['slug']); ?></td>
                                <td><?php echo $page['show_in_footer'] ? 'Yes' : 'No'; ?></td>
                                <td>
                                    <a href="page-edit.php?id=<?php echo $page['id']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="page-delete.php?id=<?php echo $page['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

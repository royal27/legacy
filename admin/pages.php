<?php
require_once 'admin_header_logic.php';
$page_title = 'Manage Pages';

// Fetch all pages
$sql = "SELECT p.id, p.slug, p.show_in_footer, pt.title
        FROM pages p
        LEFT JOIN page_translations pt ON p.id = pt.page_id AND pt.language_code = ?
        ORDER BY pt.title";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_lang);
$stmt->execute();
$pages = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pages</title>
    <link rel="stylesheet" href="../assets/css/admin_themes/<?php echo $admin_theme; ?>">
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
                            <th>Title (in <?php echo htmlspecialchars($admin_lang); ?>)</th>
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

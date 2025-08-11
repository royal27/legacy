<?php
require_once 'admin_header_logic.php';
$role = $_SESSION['user_role'];
$page_title = 'Manage Menus';

// Fetch menus with their translations for the selected admin language
$sql = "SELECT m.id, m.price, m.image, mt.name, mt.description
        FROM menus m
        LEFT JOIN menu_translations mt ON m.id = mt.menu_id AND mt.language_code = ?
        ORDER BY m.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_lang);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menus</title>
    <link rel="stylesheet" href="../assets/css/admin_themes/<?php echo $admin_theme; ?>">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <?php if ($role === 'owner'): ?>
                    <a href="menu-add.php" class="btn btn-add">Add New Menu</a>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>Name (<?php echo htmlspecialchars($admin_lang); ?>)</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Description (<?php echo htmlspecialchars($admin_lang); ?>)</th>
                        <?php if ($role === 'owner'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['price']); ?></td>
                                <td><img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" width="100"></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <?php if ($role === 'owner'): ?>
                                    <td>
                                        <a href="menu-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                                        <a href="menu-delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo ($role === 'owner') ? '5' : '4'; ?>">No menu items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
<?php
$conn->close();
?>

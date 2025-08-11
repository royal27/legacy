<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';
$page_title = 'Manage Categories';

$message = '';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = $_POST['name'];
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        $message = "Category updated successfully!";
    } else {
        // Add
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $message = "Category added successfully!";
    }
    $stmt->execute();
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = "Category deleted successfully!";
}

// Fetch all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

// Fetch category to edit if ID is in URL
$category_to_edit = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $category_to_edit = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <?php if ($message): ?>
                    <p class="message success"><?php echo $message; ?></p>
                <?php endif; ?>

                <div class="card">
                    <h3><?php echo $category_to_edit ? 'Edit Category' : 'Add New Category'; ?></h3>
                    <form action="categories.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $category_to_edit['id'] ?? ''; ?>">
                        <div class="input-group">
                            <label for="name">Category Name</label>
                            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($category_to_edit['name'] ?? ''); ?>" required>
                        </div>
                        <button type="submit"><?php echo $category_to_edit ? 'Update Category' : 'Add Category'; ?></button>
                    </form>
                </div>

                <div class="card">
                    <h3>Existing Categories</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td>
                                        <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="btn btn-primary">Edit</a>
                                        <form action="categories.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure? Menu items in this category will become uncategorized.');">
                                            <input type="hidden" name="delete_id" value="<?php echo $cat['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

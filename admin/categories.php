<?php
require_once 'admin_header_logic.php';
$page_title = 'Manage Categories';

$message = '';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['translations'])) {
    $translations = $_POST['translations'];

    $conn->begin_transaction();
    try {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $category_id = $_POST['id'];
        } else {
            // Insert new main category
            $conn->query("INSERT INTO menu_categories (id) VALUES (NULL)");
            $category_id = $conn->insert_id;
        }

        // Insert/Update translations
        $stmt = $conn->prepare("INSERT INTO category_translations (category_id, language_code, name) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
        foreach ($translations as $lang_code => $trans) {
            $name = $trans['name'];
            $stmt->bind_param("iss", $category_id, $lang_code, $name);
            $stmt->execute();
        }

        $conn->commit();
        $message = "Category saved successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    // The ON DELETE CASCADE constraint will handle deleting translations
    $stmt = $conn->prepare("DELETE FROM menu_categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = "Category deleted successfully!";
}

// Fetch all categories with their names in the current admin language
$sql = "SELECT mc.id, ct.name FROM menu_categories mc LEFT JOIN category_translations ct ON mc.id = ct.category_id AND ct.language_code = ? ORDER BY ct.name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_lang);
$stmt->execute();
$categories = $stmt->get_result();

// Fetch category to edit if ID is in URL
$category_to_edit = null;
$existing_translations = [];
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM category_translations WHERE category_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $translations_result = $stmt->get_result();
    while($row = $translations_result->fetch_assoc()) {
        $existing_translations[$row['language_code']] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../assets/css/admin_themes/<?php echo $admin_theme; ?>">
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
                    <h3><?php echo isset($_GET['edit']) ? 'Edit Category' : 'Add New Category'; ?></h3>
                    <form action="categories.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $_GET['edit'] ?? ''; ?>">

                        <?php mysqli_data_seek($available_languages, 0); ?>
                        <?php while ($lang = $available_languages->fetch_assoc()): ?>
                            <div class="input-group">
                                <label for="translations[<?php echo $lang['code']; ?>][name]">Name (<?php echo htmlspecialchars($lang['name']); ?>)</label>
                                <input type="text" name="translations[<?php echo $lang['code']; ?>][name]" value="<?php echo htmlspecialchars($existing_translations[$lang['code']]['name'] ?? ''); ?>" required>
                            </div>
                        <?php endwhile; ?>

                        <button type="submit"><?php echo isset($_GET['edit']) ? 'Update Category' : 'Add Category'; ?></button>
                    </form>
                </div>

                <div class="card">
                    <h3>Existing Categories</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Name (in <?php echo htmlspecialchars($admin_lang); ?>)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cat['name'] ?? '[No translation]'); ?></td>
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

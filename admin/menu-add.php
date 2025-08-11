<?php
require_once 'admin_header_logic.php';
if ($_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
$page_title = 'Add Menu Item';

// Fetch all languages
$languages = $conn->query("SELECT * FROM languages ORDER BY name");
// Fetch all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

    try {
        $price = $_POST['price'];
        $category_id = $_POST['category_id'];
        $translations = $_POST['translations'];
        $image = '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . $image_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $image_name;
            } else {
                throw new Exception('Error uploading image.');
            }
        } else {
            throw new Exception('Image is required.');
        }

        $conn->begin_transaction();

        $stmt_menu = $conn->prepare("INSERT INTO menus (category_id, price, image) VALUES (?, ?, ?)");
        $stmt_menu->bind_param("ids", $category_id, $price, $image);
        $stmt_menu->execute();
        $menu_id = $stmt_menu->insert_id;

        $stmt_trans = $conn->prepare("INSERT INTO menu_translations (menu_id, language_code, name, description) VALUES (?, ?, ?, ?)");
        foreach ($translations as $lang_code => $trans) {
            $name = $trans['name'];
            $description = $trans['description'];
            $stmt_trans->bind_param("isss", $menu_id, $lang_code, $name, $description);
            $stmt_trans->execute();
        }

        $conn->commit();
        $response = ['status' => 'success', 'message' => 'Menu item added successfully!'];
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item</title>
    <link rel="stylesheet" href="../assets/css/admin_themes/<?php echo $admin_theme; ?>">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <div id="ajax-message"></div>
                <form action="menu-add.php" method="post" enctype="multipart/form-data" id="add-menu-form">

                    <?php while ($lang = $languages->fetch_assoc()): ?>
                        <h3><?php echo htmlspecialchars($lang['name']); ?></h3>
                    <div class="input-group">
                        <label for="translations[<?php echo $lang['code']; ?>][name]">Name</label>
                        <input type="text" name="translations[<?php echo $lang['code']; ?>][name]" required>
                    </div>
                    <div class="input-group">
                        <label for="translations[<?php echo $lang['code']; ?>][description]">Description</label>
                        <textarea name="translations[<?php echo $lang['code']; ?>][description]" rows="3" required></textarea>
                    </div>
                <?php endwhile; ?>

                <hr>

                <h3>General Details</h3>
                <div class="input-group">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id">
                        <option value="">Uncategorized</option>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" step="0.01" required>
                </div>
                <div class="input-group">
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image" required>
                </div>
                <button type="submit">Add Menu Item</button>
            </form>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

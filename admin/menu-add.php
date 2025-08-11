<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: menus.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';

// Fetch all languages
$languages = $conn->query("SELECT * FROM languages ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $price = $_POST['price'];
    $translations = $_POST['translations'];

    // Image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_dir = "../uploads/";
        // Ensure the upload directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $image_name;
        } else {
            die("Error uploading image.");
        }
    }

    $conn->begin_transaction();

    try {
        // Insert into menus table
        $stmt_menu = $conn->prepare("INSERT INTO menus (price, image) VALUES (?, ?)");
        $stmt_menu->bind_param("ds", $price, $image);
        $stmt_menu->execute();
        $menu_id = $stmt_menu->insert_id;

        // Insert translations
        $stmt_trans = $conn->prepare("INSERT INTO menu_translations (menu_id, language_code, name, description) VALUES (?, ?, ?, ?)");
        foreach ($translations as $lang_code => $trans) {
            $name = $trans['name'];
            $description = $trans['description'];
            $stmt_trans->bind_param("isss", $menu_id, $lang_code, $name, $description);
            $stmt_trans->execute();
        }

        $conn->commit();
        header("Location: menus.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header>
            <h2>Add Menu Item</h2>
        </header>
        <main>
            <form action="menu-add.php" method="post" enctype="multipart/form-data">

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
</body>
</html>

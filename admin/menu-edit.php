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

// Fetch all languages
$languages = $conn->query("SELECT * FROM languages ORDER BY name");

// Fetch menu item
$stmt_menu = $conn->prepare("SELECT * FROM menus WHERE id = ?");
$stmt_menu->bind_param("i", $id);
$stmt_menu->execute();
$menu = $stmt_menu->get_result()->fetch_assoc();
if (!$menu) {
    header("Location: menus.php");
    exit();
}

// Fetch existing translations for this menu item
$stmt_trans = $conn->prepare("SELECT * FROM menu_translations WHERE menu_id = ?");
$stmt_trans->bind_param("i", $id);
$stmt_trans->execute();
$translations_result = $stmt_trans->get_result();
$existing_translations = [];
while($row = $translations_result->fetch_assoc()) {
    $existing_translations[$row['language_code']] = $row;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $price = $_POST['price'];
    $translations_data = $_POST['translations'];
    $current_image = $_POST['current_image'];

    // Image upload
    $image = $current_image;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $image_name;
            if ($current_image && file_exists($target_dir . $current_image)) {
                unlink($target_dir . $current_image);
            }
        } else {
            die("Error uploading image.");
        }
    }

    $conn->begin_transaction();
    try {
        // Update menu table
        $stmt_menu = $conn->prepare("UPDATE menus SET price = ?, image = ? WHERE id = ?");
        $stmt_menu->bind_param("dsi", $price, $image, $id);
        $stmt_menu->execute();

        // Update translations
        $stmt_trans_update = $conn->prepare("INSERT INTO menu_translations (menu_id, language_code, name, description) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)");
        foreach ($translations_data as $lang_code => $trans) {
            $name = $trans['name'];
            $description = $trans['description'];
            $stmt_trans_update->bind_param("isss", $id, $lang_code, $name, $description);
            $stmt_trans_update->execute();
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
    <title>Edit Menu Item</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header>
            <h2>Edit Menu Item</h2>
        </header>
        <main>
            <form action="menu-edit.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($menu['image']); ?>">

                <?php mysqli_data_seek($languages, 0); // Reset pointer ?>
                <?php while ($lang = $languages->fetch_assoc()):
                    $lang_code = $lang['code'];
                    $name = $existing_translations[$lang_code]['name'] ?? '';
                    $description = $existing_translations[$lang_code]['description'] ?? '';
                ?>
                    <h3><?php echo htmlspecialchars($lang['name']); ?></h3>
                    <div class="input-group">
                        <label>Name</label>
                        <input type="text" name="translations[<?php echo $lang_code; ?>][name]" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Description</label>
                        <textarea name="translations[<?php echo $lang_code; ?>][description]" rows="3" required><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                <?php endwhile; ?>

                <hr>

                <h3>General Details</h3>
                <div class="input-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" step="0.01" value="<?php echo htmlspecialchars($menu['price']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="image">New Image (optional)</label>
                    <input type="file" name="image" id="image">
                    <?php if ($menu['image']): ?>
                        <p>Current image: <img src="../uploads/<?php echo htmlspecialchars($menu['image']); ?>" alt="Current image" width="100"></p>
                    <?php endif; ?>
                </div>
                <button type="submit">Update Menu Item</button>
            </form>
        </main>
    </div>
</body>
</html>

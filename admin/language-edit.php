<?php
require_once 'admin_header_logic.php';
$page_title = 'Edit Language';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: languages.php");
    exit();
}

$id = $_GET['id'];

// Handle Update Language
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_language'])) {
    $name = $_POST['name'];
    $code = $_POST['code'];

    // It's important to also update the language_code in menu_translations
    $conn->begin_transaction();
    try {
        $stmt_old_lang = $conn->prepare("SELECT code FROM languages WHERE id = ?");
        $stmt_old_lang->bind_param("i", $id);
        $stmt_old_lang->execute();
        $old_lang_code = $stmt_old_lang->get_result()->fetch_assoc()['code'];

        $stmt_update_trans = $conn->prepare("UPDATE menu_translations SET language_code = ? WHERE language_code = ?");
        $stmt_update_trans->bind_param("ss", $code, $old_lang_code);
        $stmt_update_trans->execute();

        $stmt_update_lang = $conn->prepare("UPDATE languages SET name = ?, code = ? WHERE id = ?");
        $stmt_update_lang->bind_param("ssi", $name, $code, $id);
        $stmt_update_lang->execute();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        // Handle error
    }

    header("Location: languages.php");
    exit();
}

// Fetch the language
$stmt = $conn->prepare("SELECT * FROM languages WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$language = $stmt->get_result()->fetch_assoc();

if (!$language) {
    header("Location: languages.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Language</title>
    <link rel="stylesheet" href="../assets/css/admin_themes/<?php echo $admin_theme; ?>">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <div class="card">
                    <form action="language-edit.php?id=<?php echo $id; ?>" method="post">
                        <div class="input-group">
                        <label for="name">Language Name</label>
                        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($language['name']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="code">Language Code</label>
                        <input type="text" name="code" id="code" value="<?php echo htmlspecialchars($language['code']); ?>" required>
                    </div>
                    <button type="submit" name="update_language">Update Language</button>
                </form>
            </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

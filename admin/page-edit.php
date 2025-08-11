<?php
require_once 'admin_header_logic.php';

function create_slug($string) {
   $string = strtolower(trim($string));
   $string = preg_replace('/[^a-z0-9-]+/', '-', $string);
   $string = preg_replace('/-+/', '-', $string);
   return rtrim($string, '-');
}

$page_title = 'Edit Page';
$page = ['id' => '', 'slug' => '', 'show_in_footer' => 0];
$existing_translations = [];

// Handle Edit
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $page = $result->fetch_assoc();
        // Fetch translations
        $stmt_trans = $conn->prepare("SELECT * FROM page_translations WHERE page_id = ?");
        $stmt_trans->bind_param("i", $_GET['id']);
        $stmt_trans->execute();
        $translations_result = $stmt_trans->get_result();
        while($row = $translations_result->fetch_assoc()) {
            $existing_translations[$row['language_code']] = $row;
        }
    }
} else {
    $page_title = 'Add New Page';
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $slug = !empty($_POST['slug']) ? create_slug($_POST['slug']) : create_slug($_POST['translations']['en']['title'] ?? 'new-page');
    $show_in_footer = isset($_POST['show_in_footer']) ? 1 : 0;
    $translations = $_POST['translations'];

    $conn->begin_transaction();
    try {
        if (!empty($id)) { // Update
            $stmt = $conn->prepare("UPDATE pages SET slug = ?, show_in_footer = ? WHERE id = ?");
            $stmt->bind_param("sii", $slug, $show_in_footer, $id);
            $stmt->execute();
            $page_id = $id;
        } else { // Insert
            $stmt = $conn->prepare("INSERT INTO pages (slug, show_in_footer) VALUES (?, ?)");
            $stmt->bind_param("si", $slug, $show_in_footer);
            $stmt->execute();
            $page_id = $conn->insert_id;
        }

        // Insert/Update translations
        $stmt_trans = $conn->prepare("INSERT INTO page_translations (page_id, language_code, title, content) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content)");
        foreach ($translations as $lang_code => $trans) {
            $title = $trans['title'];
            $content = $trans['content'];
            $stmt_trans->bind_param("isss", $page_id, $lang_code, $title, $content);
            $stmt_trans->execute();
        }

        $conn->commit();
        header("Location: pages.php?success=1");
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: pages.php?error=1&msg=" . urlencode($e->getMessage()));
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/admin_themes/<?php echo $admin_theme; ?>">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <form action="page-edit.php?id=<?php echo $page['id']; ?>" method="post">
                    <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
                    <div class="input-group">
                        <label for="slug">URL Slug (e.g., about-us). If empty, it will be auto-generated from the English title.</label>
                        <input type="text" name="slug" id="slug" value="<?php echo htmlspecialchars($page['slug']); ?>">
                    </div>
                     <div class="input-group">
                        <label>
                            <input type="checkbox" name="show_in_footer" value="1" <?php echo $page['show_in_footer'] ? 'checked' : ''; ?>>
                            Show link in footer
                        </label>
                    </div>
                    <hr>
                    <button type="button" id="add-link-btn" class="btn">Add Link BBCode</button>

                    <?php mysqli_data_seek($available_languages, 0); ?>
                    <?php while ($lang = $available_languages->fetch_assoc()):
                        $lang_code = $lang['code'];
                    ?>
                        <h3><?php echo htmlspecialchars($lang['name']); ?></h3>
                        <div class="input-group">
                            <label for="translations[<?php echo $lang_code; ?>][title]">Title</label>
                            <input type="text" name="translations[<?php echo $lang_code; ?>][title]" value="<?php echo htmlspecialchars($existing_translations[$lang_code]['title'] ?? ''); ?>" required>
                        </div>
                        <div class="input-group">
                            <label for="translations[<?php echo $lang_code; ?>][content]">Content</label>
                            <textarea name="translations[<?php echo $lang_code; ?>][content]" class="content-textarea" rows="15"><?php echo htmlspecialchars($existing_translations[$lang_code]['content'] ?? ''); ?></textarea>
                        </div>
                    <?php endwhile; ?>

                    <button type="submit">Save Page</button>
                </form>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

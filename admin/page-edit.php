<?php
require_once 'admin_header_logic.php';

function create_slug($string) {
   $string = strtolower(trim($string));
   $string = preg_replace('/[^a-z0-9-]+/', '-', $string);
   $string = preg_replace('/-+/', '-', $string);
   return rtrim($string, '-');
}

$page_title = 'Edit Page';
$page = ['id' => '', 'title' => '', 'slug' => '', 'content' => '', 'show_in_footer' => 0];

// Handle Edit
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $page = $result->fetch_assoc();
    }
} else {
    $page_title = 'Add New Page';
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $slug = !empty($_POST['slug']) ? create_slug($_POST['slug']) : create_slug($title);
    $content = $_POST['content'];
    $show_in_footer = isset($_POST['show_in_footer']) ? 1 : 0;

    if (!empty($id)) { // Update
        $stmt = $conn->prepare("UPDATE pages SET title = ?, slug = ?, content = ?, show_in_footer = ? WHERE id = ?");
        $stmt->bind_param("sssii", $title, $slug, $content, $show_in_footer, $id);
    } else { // Insert
        $stmt = $conn->prepare("INSERT INTO pages (title, slug, content, show_in_footer) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $slug, $content, $show_in_footer);
    }

    if ($stmt->execute()) {
        header("Location: pages.php?success=1");
    } else {
        header("Location: pages.php?error=1");
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
                <form action="page-edit.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
                    <div class="input-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($page['title']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="slug">URL Slug (e.g., about-us). Leave blank to auto-generate from title.</label>
                        <input type="text" name="slug" id="slug" value="<?php echo htmlspecialchars($page['slug']); ?>">
                    </div>
                    <div class="input-group">
                        <label for="content">Content</label>
                        <button type="button" id="add-link-btn" class="btn">Add Link</button>
                        <textarea name="content" id="content" rows="15"><?php echo htmlspecialchars($page['content']); ?></textarea>
                    </div>
                    <div class="input-group">
                        <label>
                            <input type="checkbox" name="show_in_footer" value="1" <?php echo $page['show_in_footer'] ? 'checked' : ''; ?>>
                            Show link in footer
                        </label>
                    </div>
                    <button type="submit">Save Page</button>
                </form>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

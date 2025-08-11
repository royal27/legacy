<?php
define('APP_LOADED', true);
require_once 'core/bootstrap.php';

// Get slug from URL
$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    redirect('index.php');
}

// Fetch page from database
$stmt = $db->prepare("SELECT * FROM pages WHERE slug = ?");
$stmt->bind_param('s', $slug);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

// If page not found, you might want a dedicated 404 page. For now, redirect home.
if (!$page) {
    http_response_code(404);
    // You could include a 404 template here
    // include 'templates/404.php';
    redirect('index.php');
}

$page_title = htmlspecialchars($page['title']);
include 'templates/header.php';
?>

<div class="container page-content">
    <h1><?php echo htmlspecialchars($page['title']); ?></h1>
    <hr>
    <div class="content-body">
        <?php
        // Using nl2br to respect line breaks from textarea.
        // For full HTML, you would remove htmlspecialchars and use a library like HTML Purifier to prevent XSS.
        // For now, this is a safe approach.
        echo nl2br(htmlspecialchars($page['content']));
        ?>
    </div>
</div>

<style>
.page-content {
    background: #fff;
    padding: 30px;
    margin-top: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
}
.content-body {
    line-height: 1.8;
    font-size: 1.1em;
}
</style>

<?php
include 'templates/footer.php';
?>

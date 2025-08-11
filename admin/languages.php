<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';
$page_title = 'Manage Languages';

// Handle Add Language
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_language'])) {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $stmt = $conn->prepare("INSERT INTO languages (name, code) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $code);
    $stmt->execute();
    header("Location: languages.php");
    exit();
}

// Fetch all languages
$languages = $conn->query("SELECT * FROM languages ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Languages</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <div class="card">
                    <h3>Add New Language</h3>
                <form action="languages.php" method="post">
                    <div class="input-group">
                        <label for="name">Language Name (e.g., English)</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    <div class="input-group">
                        <label for="code">Language Code (e.g., en)</label>
                        <input type="text" name="code" id="code" required>
                    </div>
                    <button type="submit" name="add_language">Add Language</button>
                </form>
            </div>
            <div class="card">
                <h3>Installed Languages</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($lang = $languages->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($lang['name']); ?></td>
                                <td><?php echo htmlspecialchars($lang['code']); ?></td>
                                <td>
                                    <a href="language-edit.php?id=<?php echo $lang['id']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="language-delete.php?id=<?php echo $lang['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure? Deleting a language will also delete all menu translations for it.');">Delete</a>
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

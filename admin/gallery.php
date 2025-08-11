<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';

$message = '';

// Handle image deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image'])) {
    $image_id = $_POST['image_id'];

    // First, get the filename to delete the file
    $stmt_get = $conn->prepare("SELECT image_filename FROM gallery WHERE id = ?");
    $stmt_get->bind_param("i", $image_id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    if ($row = $result->fetch_assoc()) {
        $filename = $row['image_filename'];
        $filepath = '../uploads/' . $filename;

        // Then, delete the record from the database
        $stmt_delete = $conn->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt_delete->bind_param("i", $image_id);
        if ($stmt_delete->execute()) {
            // Finally, delete the file
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            $message = "Image deleted successfully.";
        } else {
            $message = "Error deleting image from database.";
        }
    }
}


// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gallery_image'])) {
    $file = $_FILES['gallery_image'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $image_name = time() . '_' . basename($file['name']);
        $target_dir = "../uploads/";
        // Ensure the upload directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO gallery (image_filename) VALUES (?)");
            $stmt->bind_param("s", $image_name);
            if($stmt->execute()){
                $message = "Image uploaded successfully!";
            } else {
                $message = "Error saving image to database.";
            }
        } else {
            $message = "Error uploading file.";
        }
    } else {
        $message = "Error with uploaded file.";
    }
}

// Fetch all gallery images
$gallery_images = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; }
        .gallery-item { border: 1px solid #ddd; padding: 0.5rem; text-align: center; }
        .gallery-item img { max-width: 100%; height: auto; }
        .gallery-item form { margin-top: 0.5rem; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header>
            <h2>Manage Gallery</h2>
        </header>
        <main>
            <?php if ($message): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>

            <div class="card">
                <h3>Upload New Image</h3>
                <form action="gallery.php" method="post" enctype="multipart/form-data">
                    <div class="input-group">
                        <label for="gallery_image">Select image</label>
                        <input type="file" name="gallery_image" id="gallery_image" accept="image/*" required>
                    </div>
                    <button type="submit">Upload Image</button>
                </form>
            </div>

            <div class="card">
                <h3>Gallery</h3>
                <div class="gallery-grid">
                    <?php if ($gallery_images->num_rows > 0): ?>
                        <?php while($img = $gallery_images->fetch_assoc()): ?>
                            <div class="gallery-item">
                                <img src="../uploads/<?php echo htmlspecialchars($img['image_filename']); ?>" alt="Gallery Image">
                                <form action="gallery.php" method="post" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                    <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
                                    <button type="submit" name="delete_image" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No images in the gallery.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';

$page_title = 'Manage Gallery';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

    try {
        // Handle image deletion
        if (isset($_POST['delete_image'])) {
            $image_id = $_POST['image_id'];
            $stmt_get = $conn->prepare("SELECT image_filename FROM gallery WHERE id = ?");
            $stmt_get->bind_param("i", $image_id);
            $stmt_get->execute();
            $result = $stmt_get->get_result();
            if ($row = $result->fetch_assoc()) {
                $filename = $row['image_filename'];
                $filepath = '../uploads/' . $filename;
                $stmt_delete = $conn->prepare("DELETE FROM gallery WHERE id = ?");
                $stmt_delete->bind_param("i", $image_id);
                if ($stmt_delete->execute()) {
                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                    $response = ['status' => 'success', 'message' => 'Image deleted successfully.'];
                } else {
                    $response['message'] = 'Error deleting image from database.';
                }
            }
        }

        // Handle image upload
        if (isset($_FILES['gallery_image'])) {
            $file = $_FILES['gallery_image'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $image_name = time() . '_' . basename($file['name']);
                $target_dir = "../uploads/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $target_file = $target_dir . $image_name;

                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    $stmt = $conn->prepare("INSERT INTO gallery (image_filename) VALUES (?)");
                    $stmt->bind_param("s", $image_name);
                    if($stmt->execute()){
                        $response = ['status' => 'success', 'message' => 'Image uploaded successfully!'];
                    } else {
                        $response['message'] = 'Error saving image to database.';
                    }
                } else {
                    $response['message'] = 'Error uploading file.';
                }
            } else {
                $response['message'] = 'Error with uploaded file.';
            }
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    exit();
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
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <div id="ajax-message" class="message" style="display: none;"></div>

                <div class="card">
                    <h3>Upload New Image</h3>
                    <form action="gallery.php" method="post" enctype="multipart/form-data" class="gallery-upload-form">
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
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

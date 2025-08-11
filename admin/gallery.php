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
        // Handle Deletion
        if (isset($_POST['delete_image'])) {
            $image_id = $_POST['image_id'];
            $stmt_get = $conn->prepare("SELECT media_type, media_path FROM gallery WHERE id = ?");
            $stmt_get->bind_param("i", $image_id);
            $stmt_get->execute();
            $result = $stmt_get->get_result();
            if ($row = $result->fetch_assoc()) {
                $stmt_delete = $conn->prepare("DELETE FROM gallery WHERE id = ?");
                $stmt_delete->bind_param("i", $image_id);
                if ($stmt_delete->execute()) {
                    // Only delete file if it was an upload
                    if (($row['media_type'] === 'image' || $row['media_type'] === 'video_upload') && file_exists('../uploads/' . $row['media_path'])) {
                        unlink('../uploads/' . $row['media_path']);
                    }
                    $response = ['status' => 'success', 'message' => 'Media deleted successfully.'];
                } else {
                    $response['message'] = 'Error deleting from database.';
                }
            }
        }
        // Handle Upload / Embed
        else {
            $media_type = $_POST['media_type'];
            $media_path = '';

            if ($media_type === 'video_embed') {
                $media_path = $_POST['media_path'];
                if (filter_var($media_path, FILTER_VALIDATE_URL) === FALSE) {
                    throw new Exception('Invalid URL provided for video embed.');
                }
            } else { // Handle file uploads for image and video
                if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
                    $media_path = time() . '_' . basename($_FILES['media_file']['name']);
                    $target_dir = "../uploads/";
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $target_file = $target_dir . $media_path;
                    if (!move_uploaded_file($_FILES['media_file']['tmp_name'], $target_file)) {
                        throw new Exception('Failed to move uploaded file.');
                    }
                } else {
                    throw new Exception('File upload error or no file selected.');
                }
            }

            $stmt = $conn->prepare("INSERT INTO gallery (media_type, media_path) VALUES (?, ?)");
            $stmt->bind_param("ss", $media_type, $media_path);
            if($stmt->execute()){
                $response = ['status' => 'success', 'message' => 'Media added successfully!'];
            } else {
                throw new Exception('Failed to save to database.');
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
                    <h3>Upload New Media</h3>
                    <form action="gallery.php" method="post" enctype="multipart/form-data" class="gallery-upload-form">
                        <div class="input-group">
                            <label>Media Type</label>
                            <label><input type="radio" name="media_type" value="image" checked> Image</label>
                            <label><input type="radio" name="media_type" value="video_upload"> Upload Video</label>
                            <label><input type="radio" name="media_type" value="video_embed"> Embed Video (URL)</label>
                        </div>
                        <div id="upload-field" class="input-group">
                            <label for="media_file">Select File</label>
                            <input type="file" name="media_file" id="media_file" accept="image/*,video/mp4">
                        </div>
                        <div id="embed-field" class="input-group" style="display: none;">
                            <label for="media_path">Video URL (e.g., YouTube)</label>
                            <input type="text" name="media_path" id="media_path">
                        </div>
                        <button type="submit">Upload Media</button>
                    </form>
                </div>

            <div class="card">
                <h3>Gallery</h3>
                <div class="gallery-grid">
                    <?php if ($gallery_images->num_rows > 0): ?>
                        <?php while($item = $gallery_images->fetch_assoc()): ?>
                            <div class="gallery-item">
                                <?php if ($item['media_type'] === 'image'): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($item['media_path']); ?>" alt="Gallery Image">
                                <?php else: ?>
                                    <div class="video-placeholder">
                                        <strong><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $item['media_type']))); ?></strong>
                                        <p><?php echo htmlspecialchars($item['media_path']); ?></p>
                                    </div>
                                <?php endif; ?>
                                <form action="gallery.php" method="post" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    <input type="hidden" name="image_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="delete_image" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No media in the gallery.</p>
                    <?php endif; ?>
                </div>
            </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

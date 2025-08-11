<?php
define('APP_LOADED', true);
require_once __DIR__ . '/core/bootstrap.php';

// Set header to JSON
header('Content-Type: application/json');

// --- Security Check: Must be logged in ---
if (!is_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication required.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// --- Handle Avatar Upload ---
if (isset($_FILES['avatar_image'])) {
    $file = $_FILES['avatar_image'];

    // File validation
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'File upload error. Code: ' . $file['error'];
        echo json_encode($response);
        exit;
    }
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        $response['message'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        echo json_encode($response);
        exit;
    }
    if ($file['size'] > 2097152) { // 2 MB
        $response['message'] = 'File is too large. Maximum size is 2MB.';
        echo json_encode($response);
        exit;
    }

    // Create uploads/avatars directory if it doesn't exist
    $upload_dir = __DIR__ . '/uploads/avatars/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate a unique name and move the file
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = $user_id . '_' . time() . '.' . $extension;
    $destination = $upload_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Delete old avatar if it exists
        $old_avatar_res = $db->query("SELECT profile_picture FROM users WHERE id = $user_id")->fetch_assoc();
        if ($old_avatar_res && !empty($old_avatar_res['profile_picture'])) {
            $old_avatar_path = $upload_dir . $old_avatar_res['profile_picture'];
            if (file_exists($old_avatar_path)) {
                @unlink($old_avatar_path);
            }
        }

        // Update database
        $stmt = $db->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param('si', $new_filename, $user_id);
        $stmt->execute();

        $response['status'] = 'success';
        $response['message'] = 'Profile picture updated successfully!';
        $response['filepath'] = $new_filename;
    } else {
        $response['message'] = 'Failed to move uploaded file. Check permissions.';
    }
} else {
    $response['message'] = 'No file was uploaded.';
}

echo json_encode($response);
exit;
?>

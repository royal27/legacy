<?php
define('APP_LOADED', true);
require_once 'core/bootstrap.php';

// Security: Must be logged in to edit profile
if (!is_logged_in()) {
    redirect(SITE_URL . '/login');
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// --- Handle form submission for updating details ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    validate_csrf_token();
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email address.';
        $message_type = 'error';
    } else {
        $sql = "UPDATE users SET email = ?";
        $params = ['s', $email];

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password = ?";
            $params[0] .= 's';
            $params[] = $hashed_password;
        }

        $sql .= " WHERE id = ?";
        $params[0] .= 'i';
        $params[] = $user_id;

        $stmt = $db->prepare($sql);
        $stmt->bind_param(...$params);

        if ($stmt->execute()) {
            $message = 'Profile updated successfully.';
            $message_type = 'success';
        } else {
            $message = 'Error updating profile. The email might already be in use.';
            $message_type = 'error';
        }
        $stmt->close();
    }
}


// Fetch current user data for the form
$user_res = $db->prepare("SELECT * FROM users WHERE id = ?");
$user_res->bind_param('i', $user_id);
$user_res->execute();
$user = $user_res->get_result()->fetch_assoc();

$page_title = "Edit Your Profile";
include 'templates/header.php';
?>

<div class="container">
    <h1><?php echo $page_title; ?></h1>

    <?php if ($message): ?>
    <div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="edit-profile-container">
        <div class="form-section">
            <form action="edit-profile.php" method="post">
                <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="update_profile">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
        <div class="avatar-section">
            <h3>Profile Picture</h3>
            <img src="uploads/avatars/<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'default.png'; ?>" alt="Your Avatar" id="avatar-preview" class="avatar-preview">
            <form id="avatar-upload-form">
                <div class="form-group">
                    <label for="avatar_upload">Upload a new picture</label>
                    <input type="file" id="avatar_upload" name="avatar_image" accept="image/*">
                </div>
                 <div class="progress-bar-container" style="display: none; margin-top: 10px;">
                    <div class="progress-bar"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.edit-profile-container { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; }
.avatar-preview { width: 200px; height: 200px; border-radius: 50%; object-fit: cover; margin-bottom: 20px; border: 3px solid var(--color-secondary); }
</style>

<script>
$(document).ready(function() {
    $('#avatar_upload').on('change', function() {
        var file_data = $(this).prop('files')[0];
        if (!file_data) {
            return;
        }
        var form_data = new FormData();
        form_data.append('avatar_image', file_data);

        var progressBarContainer = $('.progress-bar-container');
        var progressBar = $('.progress-bar');

        $.ajax({
            url: 'profile_ajax_handler.php',
            type: 'POST',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                progressBarContainer.show();
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = parseInt((evt.loaded / evt.total) * 100);
                        progressBar.width(percentComplete + '%');
                        progressBar.text(percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                progressBarContainer.hide();
                if (response.status === 'success') {
                    toastr.success(response.message);
                    // Update the preview image
                    var newImageUrl = 'uploads/avatars/' + response.filepath + '?t=' + new Date().getTime();
                    $('#avatar-preview').attr('src', newImageUrl);
                    $('#avatar_upload').val(''); // Clear the file input
                } else {
                    toastr.error(response.message || 'An error occurred.');
                }
            },
            error: function() {
                progressBarContainer.hide();
                toastr.error('An unexpected error occurred during upload.');
            }
        });
    });
});
</script>

<?php
include 'templates/footer.php';
?>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';
$page_title = 'Edit User';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit();
}
$user_id = $_GET['id'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ?, first_name = ?, last_name = ?, gender = ?, age = ? WHERE id = ?");
        $stmt->bind_param("ssssssii", $username, $password, $role, $first_name, $last_name, $gender, $age, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ?, first_name = ?, last_name = ?, gender = ?, age = ? WHERE id = ?");
        $stmt->bind_param("sssssii", $username, $role, $first_name, $last_name, $gender, $age, $user_id);
    }

    if ($stmt->execute()) {
        $message = "User updated successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <?php if ($message): ?>
                    <p class="message"><?php echo $message; ?></p>
                <?php endif; ?>
                <form action="user-edit.php?id=<?php echo $user_id; ?>" method="post">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="password">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" id="password">
                    </div>
                    <div class="input-group">
                        <label for="role">Role</label>
                        <select name="role" id="role" required>
                            <option value="waiter" <?php echo ($user['role'] === 'waiter') ? 'selected' : ''; ?>>Waiter</option>
                            <option value="owner" <?php echo ($user['role'] === 'owner') ? 'selected' : ''; ?>>Owner</option>
                        </select>
                    </div>
                    <hr>
                    <h3>Personal Details</h3>
                    <div class="input-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                    </div>
                    <div class="input-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                    </div>
                    <div class="input-group">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender">
                            <option value="male" <?php echo ($user['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($user['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($user['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="age">Age</label>
                        <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($user['age']); ?>">
                    </div>
                    <button type="submit">Update User</button>
                </form>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

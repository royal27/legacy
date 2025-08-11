<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';
$page_title = 'Add User';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role, first_name, last_name, gender, age) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $username, $password, $role, $first_name, $last_name, $gender, $age);

    if ($stmt->execute()) {
        $message = "User added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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
                <form action="user-add.php" method="post">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="input-group">
                        <label for="role">Role</label>
                        <select name="role" id="role" required>
                            <option value="waiter">Waiter</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                    <hr>
                    <h3>Personal Details</h3>
                    <div class="input-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name">
                    </div>
                    <div class="input-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name">
                    </div>
                    <div class="input-group">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="age">Age</label>
                        <input type="number" name="age" id="age">
                    </div>
                    <button type="submit">Add User</button>
                </form>
            </main>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

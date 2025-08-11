<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';
require_once '../includes/functions.php';
$page_title = 'Manage Offers';

$message = '';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['offer_text'])) {
    $offer_text = $_POST['offer_text'];
    if (isset($_POST['offer_id']) && !empty($_POST['offer_id'])) {
        // Update existing offer
        $offer_id = $_POST['offer_id'];
        $stmt = $conn->prepare("UPDATE offers SET offer_text = ? WHERE id = ?");
        $stmt->bind_param("si", $offer_text, $offer_id);
        $message = "Offer updated successfully!";
    } else {
        // Add new offer
        $stmt = $conn->prepare("INSERT INTO offers (offer_text) VALUES (?)");
        $stmt->bind_param("s", $offer_text);
        $message = "Offer added successfully!";
    }
    $stmt->execute();
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_offer'])) {
    $offer_id = $_POST['offer_id'];
    $stmt = $conn->prepare("DELETE FROM offers WHERE id = ?");
    $stmt->bind_param("i", $offer_id);
    $stmt->execute();
    $message = "Offer deleted successfully!";
}

// Fetch all offers
$offers = $conn->query("SELECT * FROM offers ORDER BY id DESC");

// Fetch offer to edit if ID is in URL
$offer_to_edit = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM offers WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $offer_to_edit = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Offers</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <?php include 'header.php'; ?>
            <main>
                <?php if ($message): ?>
                    <p class="message success"><?php echo $message; ?></p>
                <?php endif; ?>

                <div class="card">
                    <h3><?php echo $offer_to_edit ? 'Edit Offer' : 'Add New Offer'; ?></h3>
                    <form action="offers.php" method="post">
                        <input type="hidden" name="offer_id" value="<?php echo $offer_to_edit['id'] ?? ''; ?>">
                        <div class="input-group">
                            <label for="offer_text">Offer Text</label>
                            <input type="text" name="offer_text" id="offer_text" value="<?php echo htmlspecialchars($offer_to_edit['offer_text'] ?? ''); ?>" required>
                        </div>
                        <button type="submit"><?php echo $offer_to_edit ? 'Update Offer' : 'Add Offer'; ?></button>
                    </form>
                </div>

                <div class="card">
                    <h3>Current Offers</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Offer Text</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($offer = $offers->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($offer['offer_text']); ?></td>
                                    <td>
                                        <a href="offers.php?edit=<?php echo $offer['id']; ?>" class="btn btn-primary">Edit</a>
                                        <form action="offers.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                            <input type="hidden" name="offer_id" value="<?php echo $offer['id']; ?>">
                                            <button type="submit" name="delete_offer" class="btn btn-danger">Delete</button>
                                        </form>
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

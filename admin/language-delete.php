<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: dashboard.php");
    exit();
}
require_once '../includes/connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: languages.php");
    exit();
}

$id = $_GET['id'];

// First, get the language code
$stmt = $conn->prepare("SELECT code FROM languages WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $lang_code = $result->fetch_assoc()['code'];

    $conn->begin_transaction();
    try {
        // Delete all translations for this language
        $stmt_trans = $conn->prepare("DELETE FROM menu_translations WHERE language_code = ?");
        $stmt_trans->bind_param("s", $lang_code);
        $stmt_trans->execute();

        // Delete the language
        $stmt_lang = $conn->prepare("DELETE FROM languages WHERE id = ?");
        $stmt_lang->bind_param("i", $id);
        $stmt_lang->execute();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        // Handle error
    }
}

header("Location: languages.php");
exit();
?>

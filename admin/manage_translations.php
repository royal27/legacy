<?php
// Admin Page: Manage Translations for a specific language
session_start();

// --- Load core files and check user permissions ---
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/language.php'; // To use t() function

$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'founder';
if (!$is_logged_in) {
    header('Location: index.php');
    exit;
}

// --- Page logic ---
// Get the language code from the URL, default to 'en' if not set
$lang_code = isset($_GET['lang']) ? trim($_GET['lang']) : 'en';
$feedback_message = '';

// Handle form submission for updating existing translations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_translations'])) {
    $translations_to_update = $_POST['translations'];
    $prefix = DB_PREFIX;
    $stmt = $mysqli->prepare("UPDATE `{$prefix}translations` SET translation_value = ? WHERE lang_code = ? AND translation_key = ?");

    foreach ($translations_to_update as $key => $value) {
        $stmt->bind_param('sss', $value, $lang_code, $key);
        $stmt->execute();
    }
    $stmt->close();
    $feedback_message = t('translations_update_success', 'Translations updated successfully!');
}

// Handle form submission for adding a new translation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_translation'])) {
    $new_key = trim($_POST['new_key']);
    $new_value = trim($_POST['new_value']);

    if (!empty($new_key) && !empty($new_value)) {
        $prefix = DB_PREFIX;
        $stmt = $mysqli->prepare("INSERT INTO `{$prefix}translations` (lang_code, translation_key, translation_value) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $lang_code, $new_key, $new_value);
        if ($stmt->execute()) {
            $feedback_message = t('translation_add_success', 'New translation added successfully!');
        } else {
            $feedback_message = t('translation_add_error', 'Error adding translation: ') . $stmt->error;
        }
        $stmt->close();
    } else {
        $feedback_message = t('translation_add_empty_error', 'Translation key and value cannot be empty.');
    }
}


// Fetch all translations for the selected language
$prefix = DB_PREFIX;
$stmt = $mysqli->prepare("SELECT translation_key, translation_value FROM `{$prefix}translations` WHERE lang_code = ? ORDER BY translation_key ASC");
$stmt->bind_param('s', $lang_code);
$stmt->execute();
$result = $stmt->get_result();
$translations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// --- Load the admin template ---
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page content starts here -->
<h1><?php echo sprintf(t('manage_translations_title', 'Manage Translations for "%s"'), htmlspecialchars(strtoupper($lang_code))); ?></h1>
<p><a href="manage_languages.php"><?php echo t('back_to_languages_link', '&laquo; Back to Languages'); ?></a></p>

<?php if ($feedback_message): ?>
    <p><strong><?php echo htmlspecialchars($feedback_message); ?></strong></p>
<?php endif; ?>

<form action="" method="post">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #eee;">
                <th style="padding: 10px; text-align: left; width: 30%;"><?php echo t('table_header_trans_key', 'Translation Key'); ?></th>
                <th style="padding: 10px; text-align: left;"><?php echo t('table_header_trans_value', 'Value'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($translations as $trans): ?>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                    <code><?php echo htmlspecialchars($trans['translation_key']); ?></code>
                </td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                    <input type="text" name="translations[<?php echo htmlspecialchars($trans['translation_key']); ?>]" value="<?php echo htmlspecialchars($trans['translation_value']); ?>" style="width: 100%;">
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit" name="update_translations" style="margin-top: 20px;"><?php echo t('update_translations_button', 'Save All Changes'); ?></button>
</form>

<hr style="margin: 40px 0;">

<!-- Add New Translation Form -->
<div style="padding: 20px; background: #f9f9f9; border-radius: 5px;">
    <h3><?php echo t('add_new_translation_title', 'Add New Translation'); ?></h3>
    <form action="" method="post">
        <label for="new_key"><?php echo t('trans_key_label', 'Translation Key'); ?></label>
        <input type="text" name="new_key" id="new_key" required style="width: auto; margin-right: 20px;">

        <label for="new_value"><?php echo t('trans_value_label', 'Translation Value'); ?></label>
        <input type="text" name="new_value" id="new_value" required style="width: auto; margin-right: 20px;">

        <button type="submit" name="add_translation"><?php echo t('add_translation_button', 'Add Translation'); ?></button>
    </form>
</div>


<?php
require_once __DIR__ . '/includes/footer.php';
?>

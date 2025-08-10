<?php
// Admin Page: Manage Languages
session_start();

// --- Load core files and check user permissions ---
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/language.php';

$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'founder';
if (!$is_logged_in) {
    header('Location: index.php');
    exit;
}

// --- Page logic: Handle form submission for adding a new language ---
$feedback_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_language'])) {
    $lang_code = trim($_POST['lang_code']);
    $lang_name = trim($_POST['lang_name']);

    if (!empty($lang_code) && !empty($lang_name)) {
        // Insert into the database
        $prefix = DB_PREFIX;
        $stmt = $mysqli->prepare("INSERT INTO `{$prefix}languages` (code, name) VALUES (?, ?)");
        $stmt->bind_param('ss', $lang_code, $lang_name);
        if ($stmt->execute()) {
            $feedback_message = t('lang_add_success', 'Language added successfully!');
        } else {
            $feedback_message = t('lang_add_error', 'Error adding language: ') . $stmt->error;
        }
        $stmt->close();
    } else {
        $feedback_message = t('lang_add_empty_error', 'Language code and name cannot be empty.');
    }
}


// --- Fetch all languages from the database ---
$prefix = DB_PREFIX;
$result = $mysqli->query("SELECT id, code, name, is_default FROM `{$prefix}languages` ORDER BY name ASC");
$languages = $result->fetch_all(MYSQLI_ASSOC);


// --- Load the admin template ---
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page content starts here -->
<h1><?php echo t('manage_languages_title', 'Manage Languages'); ?></h1>
<p><?php echo t('manage_languages_description', 'Here you can view, add, and manage the languages for your site.'); ?></p>

<?php if ($feedback_message): ?>
    <p><strong><?php echo htmlspecialchars($feedback_message); ?></strong></p>
<?php endif; ?>

<!-- Add New Language Form -->
<div style="margin-bottom: 20px; padding: 20px; background: #f9f9f9; border-radius: 5px;">
    <h3><?php echo t('add_new_language_title', 'Add New Language'); ?></h3>
    <form action="" method="post">
        <label for="lang_code"><?php echo t('lang_code_label', 'Language Code (e.g., fr)'); ?></label>
        <input type="text" name="lang_code" id="lang_code" required style="width: auto; margin-right: 20px;">

        <label for="lang_name"><?php echo t('lang_name_label', 'Language Name (e.g., Français)'); ?></label>
        <input type="text" name="lang_name" id="lang_name" required style="width: auto; margin-right: 20px;">

        <button type="submit" name="add_language"><?php echo t('add_language_button', 'Add Language'); ?></button>
    </form>
</div>


<!-- List of Existing Languages -->
<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background: #eee;">
            <th style="padding: 10px; text-align: left;">ID</th>
            <th style="padding: 10px; text-align: left;"><?php echo t('table_header_lang_code', 'Code'); ?></th>
            <th style="padding: 10px; text-align: left;"><?php echo t('table_header_lang_name', 'Name'); ?></th>
            <th style="padding: 10px; text-align: left;"><?php echo t('table_header_lang_default', 'Default'); ?></th>
            <th style="padding: 10px; text-align: left;"><?php echo t('table_header_actions', 'Actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($languages as $lang): ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($lang['id']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($lang['code']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($lang['name']); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?php echo $lang['is_default'] ? t('yes', 'Yes') : t('no', 'No'); ?></td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                <a href="#"><?php echo t('action_edit', 'Edit'); ?></a> |
                <a href="manage_translations.php?lang=<?php echo htmlspecialchars($lang['code']); ?>" style="color: blue;"><?php echo t('action_manage_translations', 'Translations'); ?></a> |
                <a href="#" style="color: red;"><?php echo t('action_delete', 'Delete'); ?></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

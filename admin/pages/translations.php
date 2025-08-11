<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

$message = '';
$message_type = '';

// --- Handle Form Submission for saving all translations ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_all_translations') {
    validate_csrf_token();
    $lang_id = (int)($_POST['lang_id'] ?? 0);
    $translations = $_POST['translations'] ?? [];

    if ($lang_id > 0) {
        $db->begin_transaction();
        try {
            $stmt = $db->prepare(
                "INSERT INTO language_strings (lang_id, lang_key, lang_value)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE lang_value = VALUES(lang_value)"
            );
            foreach($translations as $key => $value) {
                $key = trim($key);
                $value = trim($value);
                $stmt->bind_param('iss', $lang_id, $key, $value);
                $stmt->execute();
            }
            $stmt->close();
            $db->commit();
            $message = 'All translations have been saved.';
            $message_type = 'success';
        } catch (mysqli_sql_exception $exception) {
            $db->rollback();
            $message = 'Error saving translations: ' . $exception->getMessage();
            $message_type = 'error';
        }
    }
}


// --- Page Display Logic ---
$lang_id = (int)($_GET['lang_id'] ?? 0);
if ($lang_id === 0) {
    redirect('index.php?page=languages');
}

// Get language details
$lang = $db->query("SELECT * FROM languages WHERE id = $lang_id")->fetch_assoc();
if (!$lang) {
    redirect('index.php?page=languages');
}

// Get all unique keys from the entire table to form a master list
$master_keys_res = $db->query("SELECT DISTINCT lang_key FROM language_strings ORDER BY lang_key ASC");
$master_keys = [];
while($row = $master_keys_res->fetch_assoc()) {
    $master_keys[$row['lang_key']] = ''; // Use key as array key for easy lookup
}

// Get all translations for the language we are editing
$translations_res = $db->query("SELECT lang_key, lang_value FROM language_strings WHERE lang_id = $lang_id");
$translations = [];
while($row = $translations_res->fetch_assoc()) {
    $translations[$row['lang_key']] = $row['lang_value'];
}

// Merge the master list with the current language's translations
$display_data = array_merge($master_keys, $translations);

?>

<div class="content-block">
    <a href="index.php?page=languages">&larr; Back to Languages</a>
    <h2>Editing Language: <strong><?php echo htmlspecialchars($lang['name']); ?></strong> (<?php echo htmlspecialchars($lang['code']); ?>)</h2>
    <p>Translations are saved automatically when you click or tab away from a text field.</p>
</div>

<div class="content-block">
    <form id="translations-form" method="post" action="index.php?page=translations&lang_id=<?php echo $lang_id; ?>">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="save_all_translations">
        <input type="hidden" name="lang_id" value="<?php echo $lang_id; ?>">
        <?php foreach ($display_data as $key => $value): ?>
            <div class="form-group translation-row">
                <label for="key-<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($key); ?></label>
                <textarea
                    id="key-<?php echo htmlspecialchars($key); ?>"
                    class="translation-input"
                    name="translations[<?php echo htmlspecialchars($key); ?>]"
                    rows="2"
                    data-key="<?php echo htmlspecialchars($key); ?>"
                ><?php echo htmlspecialchars($value); ?></textarea>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary">Save All Translations</button>
    </form>
</div>

<style>
.translation-row { margin-bottom: 20px; }
.translation-row label { font-weight: bold; display: block; margin-bottom: 5px; font-family: monospace; color: var(--color-accent); }
</style>

<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}

// --- AJAX Handler ---
// This part handles the POST request from the JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

    $lang_id = (int)($_POST['lang_id'] ?? 0);
    $lang_key = trim($_POST['lang_key'] ?? '');
    $lang_value = trim($_POST['lang_value'] ?? '');

    if ($lang_id > 0 && !empty($lang_key)) {
        // Use INSERT ... ON DUPLICATE KEY UPDATE to handle both new and existing translations
        $stmt = $db->prepare(
            "INSERT INTO language_strings (lang_id, lang_key, lang_value)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE lang_value = VALUES(lang_value)"
        );
        $stmt->bind_param('iss', $lang_id, $lang_key, $lang_value);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Translation saved!';
        } else {
            $response['message'] = 'Database error: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['message'] = 'Invalid data received.';
    }

    echo json_encode($response);
    exit;
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
    <form id="translations-form">
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
    </form>
</div>

<style>
.translation-row { margin-bottom: 20px; }
.translation-row label { font-weight: bold; display: block; margin-bottom: 5px; font-family: monospace; color: var(--color-accent); }
.translation-input {
    border-color: #ccc;
}
.translation-input:focus {
    border-color: var(--color-secondary);
    box-shadow: 0 0 5px rgba(138, 43, 226, 0.5);
}
</style>

<script>
$(document).ready(function() {
    $('.translation-input').on('blur', function() {
        var textarea = $(this);
        var lang_id = $('#translations-form input[name="lang_id"]').val();
        var lang_key = textarea.data('key');
        var lang_value = textarea.val();

        // Add a visual cue that something is happening
        textarea.css('border-color', 'orange');

        $.ajax({
            url: 'index.php?page=translations', // Post to the same page
            type: 'POST',
            dataType: 'json',
            data: {
                lang_id: lang_id,
                lang_key: lang_key,
                lang_value: lang_value
            },
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    textarea.css('border-color', '#28a745'); // Green for success
                } else {
                    toastr.error(response.message || 'An error occurred.');
                    textarea.css('border-color', '#dc3545'); // Red for error
                }
            },
            error: function() {
                toastr.error('An unexpected error occurred. Please check the console.');
                textarea.css('border-color', '#dc3545');
            },
            complete: function() {
                // Optional: remove color after a delay
                setTimeout(function() {
                    textarea.css('border-color', '#ccc');
                }, 2000);
            }
        });
    });
});
</script>

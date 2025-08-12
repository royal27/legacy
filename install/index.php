<?php
// Start the session to store installation settings
session_start();

// --- Language Setup ---
// Set a default language and supported languages
$DEFAULT_LANG = 'en';
$SUPPORTED_LANGS = ['en', 'ro'];
$lang = $DEFAULT_LANG;

// If a language is already in the session, use it
if (isset($_SESSION['install_language']) && in_array($_SESSION['install_language'], $SUPPORTED_LANGS)) {
    $lang = $_SESSION['install_language'];
}

// Language strings
$t = [
    'en' => [
        'step1_title' => 'Step 1: Select Language',
        'step1_select' => 'Select your preferred language for the installation:',
        'language' => 'Language',
        'continue' => 'Continue',
        'step2_title' => 'Step 2: Database Configuration',
        'step2_intro' => 'Please provide your database details below. The installer will attempt to verify them.',
        'db_host' => 'Database Host',
        'db_name' => 'Database Name',
        'db_user' => 'Database User',
        'db_pass' => 'Database Password',
        'db_prefix' => 'Table Prefix',
        'db_test_failed' => 'Database connection failed. Please check your credentials.',
        'db_test_success' => 'Database connection successful!',
        'back' => 'Back',
        'step3_title' => 'Step 3: Founder Registration',
        'step3_intro' => 'Create the main administrator account (Founder).',
        'username' => 'Username',
        'email' => 'Email Address',
        'password' => 'Password',
        'password_confirm' => 'Confirm Password',
        'finish_install' => 'Finish Installation',
        'install_success_title' => 'Installation Complete!',
        'install_success_msg' => 'Congratulations! The application has been installed successfully. You can now log in.',
        'goto_homepage' => 'Go to Homepage',
        'error_pass_mismatch' => 'Passwords do not match.',
        'error_pass_short' => 'Password must be at least 8 characters long.',
        'error_email_invalid' => 'Invalid email address.',
        'error_all_fields' => 'Please fill in all fields.',
        'error_install' => 'An error occurred during installation: ',
    ],
    'ro' => [
        'step1_title' => 'Pasul 1: Selectați Limba',
        'step1_select' => 'Selectați limba preferată pentru procesul de instalare:',
        'language' => 'Limba',
        'continue' => 'Continuă',
        'step2_title' => 'Pasul 2: Configurare Bază de Date',
        'step2_intro' => 'Vă rugăm să introduceți detaliile bazei de date. Instalatorul va încerca să le verifice.',
        'db_host' => 'Host Bază de Date',
        'db_name' => 'Nume Bază de Date',
        'db_user' => 'Utilizator Bază de Date',
        'db_pass' => 'Parolă Bază de Date',
        'db_prefix' => 'Prefix Tabele',
        'db_test_failed' => 'Conexiunea la baza de date a eșuat. Vă rugăm verificați datele introduse.',
        'db_test_success' => 'Conexiune la baza de date realizată cu succes!',
        'back' => 'Înapoi',
        'step3_title' => 'Pasul 3: Înregistrare Fondator',
        'step3_intro' => 'Creați contul principal de administrator (Fondator).',
        'username' => 'Nume de utilizator',
        'email' => 'Adresă de email',
        'password' => 'Parolă',
        'password_confirm' => 'Confirmare Parolă',
        'finish_install' => 'Finalizează Instalarea',
        'install_success_title' => 'Instalare Completă!',
        'install_success_msg' => 'Felicitări! Aplicația a fost instalată cu succes. Acum vă puteți autentifica.',
        'goto_homepage' => 'Mergi la Pagina Principală',
        'error_pass_mismatch' => 'Parolele nu se potrivesc.',
        'error_pass_short' => 'Parola trebuie să aibă cel puțin 8 caractere.',
        'error_email_invalid' => 'Adresă de email invalidă.',
        'error_all_fields' => 'Vă rugăm să completați toate câmpurile.',
        'error_install' => 'A apărut o eroare în timpul instalării: ',
    ]
];

// Determine the current step. Default to 1.
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
// Prevent access to installer if config file exists
if (file_exists('../app/core/config.php') && $step < 4) {
    die("Installer is locked. Please remove 'app/core/config.php' to reinstall.");
}
$error_message = '';
$success_message = '';

// --- Step 1: Language Selection Logic ---
if ($step === 1) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['language'])) {
        $selected_lang = $_POST['language'];
        if (in_array($selected_lang, $SUPPORTED_LANGS)) {
            $_SESSION['install_language'] = $selected_lang;
            header('Location: index.php?step=2');
            exit;
        }
    }
    $pageTitle = $t[$lang]['step1_title'];
    $content = '
        <form action="index.php?step=1" method="post">
            <p>' . $t[$lang]['step1_select'] . '</p>
            <label for="language">' . $t[$lang]['language'] . ':</label>
            <select name="language" id="language" required>
                <option value="en" ' . ($lang === 'en' ? 'selected' : '') . '>English</option>
                <option value="ro" ' . ($lang === 'ro' ? 'selected' : '') . '>Română</option>
            </select>
            <br><br>
            <button type="submit">' . $t[$lang]['continue'] . ' &rarr;</button>
        </form>
    ';
}
// --- Step 2: Database Configuration ---
elseif ($step === 2) {
    // Redirect to step 1 if no language is set
    if (!isset($_SESSION['install_language'])) {
        header('Location: index.php?step=1');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db_host = $_POST['db_host'];
        $db_name = $_POST['db_name'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_pass'];
        $db_prefix = $_POST['db_prefix'];

        // Simple validation
        if (empty($db_host) || empty($db_name) || empty($db_user) || empty($db_prefix)) {
            $error_message = 'Please fill in all required fields.';
        } else {
            // Test connection
            try {
                $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
                if ($conn->connect_error) {
                    throw new Exception($conn->connect_error);
                }
                // Connection successful
                $_SESSION['db_credentials'] = $_POST;
                $conn->close();
                header('Location: index.php?step=3');
                exit;
            } catch (Exception $e) {
                $error_message = $t[$lang]['db_test_failed'] . ' (' . $e->getMessage() . ')';
            }
        }
    }

    $pageTitle = $t[$lang]['step2_title'];
    $content = '
        <form action="index.php?step=2" method="post">
            <p>' . $t[$lang]['step2_intro'] . '</p>
            ' . ($error_message ? '<p style="color:red;">' . $error_message . '</p>' : '') . '
            <label for="db_host">' . $t[$lang]['db_host'] . ':</label>
            <input type="text" name="db_host" id="db_host" value="localhost" required>

            <label for="db_name">' . $t[$lang]['db_name'] . ':</label>
            <input type="text" name="db_name" id="db_name" required>

            <label for="db_user">' . $t[$lang]['db_user'] . ':</label>
            <input type="text" name="db_user" id="db_user" required>

            <label for="db_pass">' . $t[$lang]['db_pass'] . ':</label>
            <input type="password" name="db_pass" id="db_pass">

            <label for="db_prefix">' . $t[$lang]['db_prefix'] . ':</label>
            <input type="text" name="db_prefix" id="db_prefix" value="core_" required>
            <br><br>
            <a href="index.php?step=1" class="button-back">&larr; ' . $t[$lang]['back'] . '</a>
            <button type="submit" style="float: right;">' . $t[$lang]['continue'] . ' &rarr;</button>
        </form>
    ';
}
// --- Step 3: Founder Registration ---
elseif ($step === 3) {
    // Redirect if previous steps are not completed
    if (!isset($_SESSION['install_language']) || !isset($_SESSION['db_credentials'])) {
        header('Location: index.php?step=1');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $error_message = $t[$lang]['error_all_fields'];
        } elseif (strlen($password) < 8) {
            $error_message = $t[$lang]['error_pass_short'];
        } elseif ($password !== $password_confirm) {
            $error_message = $t[$lang]['error_pass_mismatch'];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = $t[$lang]['error_email_invalid'];
        } else {
            // All good, proceed with installation
            try {
                // 1. Create config file
                $config_content = "<?php\n\n";
                $config_content .= "// -- DATABASE SETTINGS -- //\n";
                $config_content .= "define('DB_HOST', '" . addslashes($_SESSION['db_credentials']['db_host']) . "');\n";
                $config_content .= "define('DB_NAME', '" . addslashes($_SESSION['db_credentials']['db_name']) . "');\n";
                $config_content .= "define('DB_USER', '" . addslashes($_SESSION['db_credentials']['db_user']) . "');\n";
                $config_content .= "define('DB_PASS', '" . addslashes($_SESSION['db_credentials']['db_pass']) . "');\n";
                $config_content .= "define('DB_PREFIX', '" . addslashes($_SESSION['db_credentials']['db_prefix']) . "');\n";

                if (!file_put_contents('../app/core/config.php', $config_content)) {
                    throw new Exception("Could not write config file. Please check permissions for 'app/core/'.");
                }

                // 2. Create database tables
                $db = new mysqli(
                    $_SESSION['db_credentials']['db_host'],
                    $_SESSION['db_credentials']['db_user'],
                    $_SESSION['db_credentials']['db_pass'],
                    $_SESSION['db_credentials']['db_name']
                );
                if ($db->connect_error) { throw new Exception("DB connection failed after writing config."); }

                $prefix = $_SESSION['db_credentials']['db_prefix'];
                $sql = file_get_contents('schema.sql');
                $sql = str_replace('%%PREFIX%%', $prefix, $sql);

                if (!$db->multi_query($sql)) {
                    throw new Exception("Failed to create database tables: " . $db->error);
                }
                // Clear multi_query results
                while ($db->more_results() && $db->next_result()) {;}

                // 3. Insert founder user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO `{$prefix}users` (username, email, password, role_id, created_at) VALUES (?, ?, ?, 1, NOW())");
                $stmt->bind_param('sss', $username, $email, $hashed_password);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to create founder account: " . $stmt->error);
                }
                $stmt->close();
                $db->close();

                // 4. Cleanup and redirect to success page
                session_destroy();
                header('Location: index.php?step=4');
                exit;

            } catch (Exception $e) {
                $error_message = $t[$lang]['error_install'] . $e->getMessage();
                // Clean up failed install
                if (file_exists('../app/core/config.php')) {
                    unlink('../app/core/config.php');
                }
            }
        }
    }

    $pageTitle = $t[$lang]['step3_title'];
    $content = '
        <form action="index.php?step=3" method="post">
            <p>' . $t[$lang]['step3_intro'] . '</p>
            ' . ($error_message ? '<p style="color:red;">' . $error_message . '</p>' : '') . '

            <label for="username">' . $t[$lang]['username'] . ':</label>
            <input type="text" name="username" id="username" required>

            <label for="email">' . $t[$lang]['email'] . ':</label>
            <input type="email" name="email" id="email" required>

            <label for="password">' . $t[$lang]['password'] . ':</label>
            <input type="password" name="password" id="password" required>

            <label for="password_confirm">' . $t[$lang]['password_confirm'] . ':</label>
            <input type="password" name="password_confirm" id="password_confirm" required>
            <br><br>
            <a href="index.php?step=2" class="button-back">&larr; ' . $t[$lang]['back'] . '</a>
            <button type="submit" style="float: right;">' . $t[$lang]['finish_install'] . ' &rarr;</button>
        </form>
    ';
}
// --- Step 4: Installation Complete ---
elseif ($step === 4) {
    $pageTitle = $t[$lang]['install_success_title'];
    $content = '
        <div style="text-align: center;">
            <p>' . $t[$lang]['install_success_msg'] . '</p>
            <a href="../index.php" class="button">' . $t[$lang]['goto_homepage'] . '</a>
        </div>
    ';
}
// --- Fallback for invalid steps ---
else {
    header('Location: index.php?step=1');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - <?php echo htmlspecialchars($pageTitle); ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; margin: 0; padding: 2rem; }
        .installer-container { max-width: 600px; margin: 2rem auto; background-color: #fff; border: 1px solid #dee2e6; border-radius: 0.5rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1); }
        .installer-header { padding: 1.5rem; border-bottom: 1px solid #dee2e6; background-color: #f1f3f5; border-radius: 0.5rem 0.5rem 0 0; }
        .installer-header h1 { margin: 0; font-size: 1.5rem; }
        .installer-content { padding: 1.5rem; }
        h2 { margin-top: 0; }
        label { font-weight: 600; }
        select, input[type="text"], input[type="password"] { width: 100%; padding: 0.5rem; margin-top: 0.25rem; border: 1px solid #ced4da; border-radius: 0.25rem; box-sizing: border-box; }
        button { display: inline-block; font-weight: 600; color: #fff; background-color: #007bff; border: 1px solid #007bff; padding: 0.5rem 1rem; font-size: 1rem; border-radius: 0.25rem; text-decoration: none; cursor: pointer; transition: background-color 0.15s ease-in-out; }
        button:hover { background-color: #0056b3; }
        a { color: #007bff; }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1>Site Installer</h1>
        </div>
        <div class="installer-content">
            <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
            <?php echo $content; ?>
        </div>
    </div>
</body>
</html>

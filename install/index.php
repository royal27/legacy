<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'install_logic.php';

// Simple multi-language array for the installer
$lang_data = [
    'en' => [
        'install_title' => 'Project Installer',
        'step' => 'Step',
        'lang_select_title' => '1. Select Language',
        'lang_select_desc' => 'Choose your preferred language for the application.',
        'language' => 'Language',
        'continue' => 'Continue',
        'db_config_title' => '2. Database Configuration',
        'db_config_desc' => 'Please provide your database connection details.',
        'db_host' => 'Database Host',
        'db_name' => 'Database Name',
        'db_user' => 'Database Username',
        'db_pass' => 'Database Password',
        'db_prefix' => 'Table Prefix',
        'founder_title' => '3. Founder Account',
        'founder_desc' => 'Create the main administrator account.',
        'username' => 'Username',
        'email' => 'Email Address',
        'password' => 'Password',
        'install_now' => 'Install Now',
        'error_notice' => 'An error occurred',
        'success_notice' => 'Success',
        'install_complete' => 'Installation Complete!',
        'install_complete_desc' => 'Your site is ready. For security reasons, please <strong>delete the "install" directory</strong> immediately.',
        'goto_site' => 'Go to your site',
    ],
    'ro' => [
        'install_title' => 'Instalare Proiect',
        'step' => 'Pasul',
        'lang_select_title' => '1. Selectare Limbă',
        'lang_select_desc' => 'Alegeți limba preferată pentru aplicație.',
        'language' => 'Limbă',
        'continue' => 'Continuă',
        'db_config_title' => '2. Configurare Bază de Date',
        'db_config_desc' => 'Vă rugăm să introduceți detaliile de conectare la baza de date.',
        'db_host' => 'Host Bază de Date',
        'db_name' => 'Nume Bază de Date',
        'db_user' => 'Utilizator Bază de Date',
        'db_pass' => 'Parolă Bază de Date',
        'db_prefix' => 'Prefix Tabele',
        'founder_title' => '3. Cont Fondator',
        'founder_desc' => 'Creați contul principal de administrator.',
        'username' => 'Nume utilizator',
        'email' => 'Adresă de email',
        'password' => 'Parolă',
        'install_now' => 'Instalează Acum',
        'error_notice' => 'A apărut o eroare',
        'success_notice' => 'Succes',
        'install_complete' => 'Instalare Completă!',
        'install_complete_desc' => 'Site-ul dumneavoastră este gata. Din motive de securitate, vă rugăm să <strong>ștergeți imediat directorul "install"</strong>.',
        'goto_site' => 'Accesează site-ul',
    ]
];

// Determine current step
$step = isset($_GET['step']) ? $_GET['step'] : '1';
$error = null;

// Process POST data and move to next step
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === '1' && isset($_POST['language'])) {
        $_SESSION['install_lang'] = $_POST['language'];
        header('Location: index.php?step=2');
        exit;
    } elseif ($step === '2' && isset($_POST['db_host'])) {
        $_SESSION['db_details'] = [
            'db_host' => $_POST['db_host'],
            'db_name' => $_POST['db_name'],
            'db_user' => $_POST['db_user'],
            'db_pass' => $_POST['db_pass'],
            'db_prefix' => $_POST['db_prefix']
        ];

        @$mysqli = new mysqli($_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_name']);
        if ($mysqli->connect_error) {
            $error = "Database connection failed: " . $mysqli->connect_error;
        } else {
            $mysqli->close();
            header('Location: index.php?step=3');
            exit;
        }
    } elseif ($step === '3' && isset($_POST['username'])) {
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
            $error = "All fields are required for the founder account.";
        } else {
            $_SESSION['founder_details'] = [
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password']
            ];
            header('Location: index.php?step=finish');
            exit;
        }
    }
}

// Set language for the installer interface
$lang_key = isset($_SESSION['install_lang']) ? $_SESSION['install_lang'] : 'en';
$lang = $lang_data[$lang_key];

?>
<!DOCTYPE html>
<html lang="<?= $lang_key ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['install_title'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1><?= $lang['install_title'] ?></h1>
        </div>
        <div class="installer-content">
            <?php if ($error): ?>
                <div class="notice error">
                    <strong><?= $lang['error_notice'] ?>:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($step === '1'): ?>
                <h2><?= $lang['step'] ?> 1: <?= $lang['lang_select_title'] ?></h2>
                <p><?= $lang['lang_select_desc'] ?></p>
                <form action="index.php?step=1" method="POST">
                    <div class="form-group">
                        <label for="language"><?= $lang['language'] ?></label>
                        <select name="language" id="language" required>
                            <option value="en" <?= $lang_key === 'en' ? 'selected' : '' ?>>English</option>
                            <option value="ro" <?= $lang_key === 'ro' ? 'selected' : '' ?>>Română</option>
                        </select>
                    </div>
                    <button type="submit" class="btn"><?= $lang['continue'] ?></button>
                </form>

            <?php elseif ($step === '2'): ?>
                <h2><?= $lang['step'] ?> 2: <?= $lang['db_config_title'] ?></h2>
                <p><?= $lang['db_config_desc'] ?></p>
                <form action="index.php?step=2" method="POST">
                    <div class="form-group">
                        <label for="db_host"><?= $lang['db_host'] ?></label>
                        <input type="text" name="db_host" id="db_host" value="<?= htmlspecialchars($_SESSION['db_details']['db_host'] ?? 'localhost') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="db_name"><?= $lang['db_name'] ?></label>
                        <input type="text" name="db_name" id="db_name" value="<?= htmlspecialchars($_SESSION['db_details']['db_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="db_user"><?= $lang['db_user'] ?></label>
                        <input type="text" name="db_user" id="db_user" value="<?= htmlspecialchars($_SESSION['db_details']['db_user'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="db_pass"><?= $lang['db_pass'] ?></label>
                        <input type="password" name="db_pass" id="db_pass">
                    </div>
                     <div class="form-group">
                        <label for="db_prefix"><?= $lang['db_prefix'] ?></label>
                        <input type="text" name="db_prefix" id="db_prefix" value="<?= htmlspecialchars($_SESSION['db_details']['db_prefix'] ?? 'core_') ?>">
                    </div>
                    <button type="submit" class="btn"><?= $lang['continue'] ?></button>
                </form>

            <?php elseif ($step === '3'): ?>
                <h2><?= $lang['step'] ?> 3: <?= $lang['founder_title'] ?></h2>
                <p><?= $lang['founder_desc'] ?></p>
                <form action="index.php?step=3" method="POST">
                    <div class="form-group">
                        <label for="username"><?= $lang['username'] ?></label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><?= $lang['email'] ?></label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><?= $lang['password'] ?></label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <button type="submit" class="btn"><?= $lang['install_now'] ?></button>
                </form>

            <?php elseif ($step === 'finish'): ?>
                <h2><?= $lang['install_title'] ?></h2>
                <?php
                    $config_created = create_config_file($_SESSION['db_details']);
                    if (!$config_created) {
                        echo '<div class="notice error">Could not write to config file. Please check permissions for the /config directory.</div>';
                    } else {
                        echo '<div class="notice success">Configuration file created successfully.</div>';

                        $db_creation_result = create_database_tables($_SESSION['db_details'], $_SESSION['founder_details']);

                        if ($db_creation_result !== true) {
                            echo '<div class="notice error">' . htmlspecialchars($db_creation_result) . '</div>';
                        } else {
                            echo '<div class="notice success">Database tables created and founder account inserted.</div>';
                            echo '<hr>';
                            echo '<h2>' . $lang['install_complete'] . '</h2>';
                            echo '<p>' . $lang['install_complete_desc'] . '</p>';

                            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
                            $host = $_SERVER['HTTP_HOST'];
                            $path = rtrim(str_replace('/install', '', dirname($_SERVER['PHP_SELF'])), '/');
                            $site_url = "{$protocol}://{$host}{$path}";
                            echo '<a href="' . $site_url . '" class="btn">' . $lang['goto_site'] . '</a>';

                            // Clean up session
                            session_destroy();
                        }
                    }
                ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

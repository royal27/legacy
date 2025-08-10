<?php
// Start the session to store installation progress
session_start();

// Set the current step. If no step is set, default to 1.
$step = isset($_SESSION['step']) ? $_SESSION['step'] : 1;

// --- Language Data (Temporary) ---
// This will be replaced by the database language manager post-installation.
$languages = [
    'en' => 'English',
    'ro' => 'Română',
];

// --- Form Processing ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step1_submit'])) {
        $selected_lang = $_POST['language'];
        if (isset($languages[$selected_lang])) {
            $_SESSION['language'] = $selected_lang;
            $_SESSION['step'] = 2;
            // Reload the page to show the next step
            header('Location: index.php');
            exit;
        }
    }

    if (isset($_POST['step2_submit'])) {
        // Store database details in session
        $_SESSION['db_host'] = $_POST['db_host'];
        $_SESSION['db_user'] = $_POST['db_user'];
        $_SESSION['db_pass'] = $_POST['db_pass'];
        $_SESSION['db_name'] = $_POST['db_name'];
        $_SESSION['db_prefix'] = $_POST['db_prefix'];

        // Move to the next step
        $_SESSION['step'] = 3;
        header('Location: index.php');
        exit;
    }

    if (isset($_POST['step3_submit'])) {
        // Final step: process all data and install the application
        $_SESSION['founder_user'] = $_POST['founder_user'];
        $_SESSION['founder_email'] = $_POST['founder_email'];
        $_SESSION['founder_pass'] = $_POST['founder_pass'];

        // Redirect to a new file to handle the actual installation logic
        // This keeps this file cleaner.
        header('Location: install_process.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Installer</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; color: #333; line-height: 1.6; padding: 20px; }
        .container { max-width: 600px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2 { text-align: center; color: #444; }
        .step { display: none; }
        .step.active { display: block; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            display: block;
            width: 100%;
            padding: 12px;
            border: none;
            background: #5cb85c;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { background: #4cae4c; }
        .steps-nav { text-align: center; margin-bottom: 20px; }
        .steps-nav span { padding: 10px 15px; background: #eee; border-radius: 5px; margin: 0 5px; }
        .steps-nav span.active { background: #5cb85c; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Website Installer</h1>

        <div class="steps-nav">
            <span class="<?php echo $step == 1 ? 'active' : ''; ?>">Step 1: Language</span>
            <span class="<?php echo $step == 2 ? 'active' : ''; ?>">Step 2: Database</span>
            <span class="<?php echo $step == 3 ? 'active' : ''; ?>">Step 3: Founder</span>
        </div>

        <!-- Step 1: Language Selection -->
        <div id="step1" class="step <?php if ($step == 1) echo 'active'; ?>">
            <h2>Choose Your Language</h2>
            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="language">Language</label>
                    <select name="language" id="language">
                        <?php foreach ($languages as $code => $name): ?>
                            <option value="<?php echo htmlspecialchars($code); ?>"><?php echo htmlspecialchars($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="step1_submit">Continue</button>
            </form>
        </div>

        <!-- Step 2: Database Details -->
        <div id="step2" class="step <?php if ($step == 2) echo 'active'; ?>">
            <h2>Database Configuration</h2>
            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" name="db_host" id="db_host" value="localhost" required>
                </div>
                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" name="db_name" id="db_name" required>
                </div>
                <div class="form-group">
                    <label for="db_user">Database Username</label>
                    <input type="text" name="db_user" id="db_user" required>
                </div>
                <div class.form-group>
                    <label for="db_pass">Database Password</label>
                    <input type="password" name="db_pass" id="db_pass">
                </div>
                <div class="form-group">
                    <label for="db_prefix">Table Prefix</label>
                    <input type="text" name="db_prefix" id="db_prefix" value="core_">
                </div>
                <button type="submit" name="step2_submit">Continue</button>
            </form>
        </div>

        <!-- Step 3: Founder Account -->
        <div id="step3" class="step <?php if ($step == 3) echo 'active'; ?>">
            <h2>Create Founder Account</h2>
            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="founder_user">Username</label>
                    <input type="text" name="founder_user" id="founder_user" required>
                </div>
                <div class="form-group">
                    <label for="founder_email">Email</label>
                    <input type="email" name="founder_email" id="founder_email" required>
                </div>
                <div class="form-group">
                    <label for="founder_pass">Password</label>
                    <input type="password" name="founder_pass" id="founder_pass" required>
                </div>
                <button type="submit" name="step3_submit">Finish Installation</button>
            </form>
        </div>
    </div>
</body>
</html>

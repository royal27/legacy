<?php
// Enable full error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session to store installation progress
session_start();

$step = isset($_SESSION['step']) ? $_SESSION['step'] : 1;
$languages = ['en' => 'English', 'ro' => 'Română'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step1_submit'])) {
        $_SESSION['language'] = $_POST['language'];
        $_SESSION['step'] = 2;
        header('Location: index.php');
        exit;
    }
    if (isset($_POST['step2_submit'])) {
        $_SESSION['db_host'] = $_POST['db_host'];
        $_SESSION['db_user'] = $_POST['db_user'];
        $_SESSION['db_pass'] = $_POST['db_pass'];
        $_SESSION['db_name'] = $_POST['db_name'];
        $_SESSION['db_prefix'] = $_POST['db_prefix'];
        $_SESSION['step'] = 3;
        header('Location: index.php');
        exit;
    }
    if (isset($_POST['step3_submit'])) {
        $_SESSION['founder_user'] = $_POST['founder_user'];
        $_SESSION['founder_email'] = $_POST['founder_email'];
        $_SESSION['founder_pass'] = $_POST['founder_pass'];
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
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0; padding: 20px; color: #fff;
            background-image: linear-gradient(135deg, violet, blue, red);
            background-attachment: fixed; background-size: cover;
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
        }
        .container {
            max-width: 600px; width: 100%;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 40px; border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        h1, h2 { text-align: center; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .step { display: none; }
        .step.active { display: block; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], input[type="password"], input[type="email"], select {
            width: 100%; padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 5px; box-sizing: border-box;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        input::placeholder { color: #eee; }
        select option { color: #333; }
        button {
            display: block; width: 100%; padding: 12px;
            border: none;
            background-image: linear-gradient(135deg, #8e2de2, #4a00e0);
            color: white; font-size: 16px; font-weight: bold;
            border-radius: 5px; cursor: pointer; transition: all 0.3s;
        }
        button:hover { box-shadow: 0 0 15px rgba(255,255,255,0.5); }
        .steps-nav { text-align: center; margin-bottom: 30px; }
        .steps-nav span { padding: 10px 15px; background: rgba(0,0,0,0.2); border-radius: 5px; margin: 0 5px; }
        .steps-nav span.active { background: #fff; color: #4a00e0; font-weight: bold; }
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

        <div id="step2" class="step <?php if ($step == 2) echo 'active'; ?>">
            <h2>Database Configuration</h2>
            <form action="index.php" method="post">
                <div class="form-group"><label for="db_host">Database Host</label><input type="text" name="db_host" id="db_host" value="localhost" required></div>
                <div class="form-group"><label for="db_name">Database Name</label><input type="text" name="db_name" id="db_name" required></div>
                <div class="form-group"><label for="db_user">Database Username</label><input type="text" name="db_user" id="db_user" required></div>
                <div class="form-group"><label for="db_pass">Database Password</label><input type="password" name="db_pass" id="db_pass"></div>
                <div class="form-group"><label for="db_prefix">Table Prefix</label><input type="text" name="db_prefix" id="db_prefix" value="core_"></div>
                <button type="submit" name="step2_submit">Continue</button>
            </form>
        </div>

        <div id="step3" class="step <?php if ($step == 3) echo 'active'; ?>">
            <h2>Create Founder Account</h2>
            <form action="index.php" method="post">
                <div class="form-group"><label for="founder_user">Username</label><input type="text" name="founder_user" id="founder_user" required></div>
                <div class="form-group"><label for="founder_email">Email</label><input type="email" name="founder_email" id="founder_email" required></div>
                <div class="form-group"><label for="founder_pass">Password</label><input type="password" name="founder_pass" id="founder_pass" required></div>
                <button type="submit" name="step3_submit">Finish Installation</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
// Prevent direct file access
if (!defined('APP_LOADED')) {
    http_response_code(403);
    die('Forbidden');
}
?>
<div class="message-box error">
    <h1>Site Not Installed</h1>
    <p>It looks like the configuration file is missing. Your website is not yet installed or configured.</p>
    <p>If you are the administrator, please go to the installation directory to set up your website.</p>
    <a href="install/index.php" class="btn btn-accent">Go to Installer</a>
</div>

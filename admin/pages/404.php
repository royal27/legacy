<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}
?>
<div class="message-box error" style="text-align: left;">
    <h2>Error 404 - Page Not Found</h2>
    <p>The page you are looking for does not exist or has been moved.</p>
    <p>Please check the URL or go back to the dashboard.</p>
    <a href="index.php?page=dashboard" class="btn btn-primary">Go to Dashboard</a>
</div>

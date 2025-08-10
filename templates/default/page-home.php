<h1><?php echo t('home_welcome_title', 'Welcome to the Homepage'); ?></h1>
<p><?php echo t('home_welcome_message', 'This is the main content of the homepage. The template system is working!'); ?></p>
<p>
    <?php
        echo "<b>" . t('test_db_connection', 'Database Connection Test:') . "</b><br>";
        $prefix = DB_PREFIX;
        $result = $mysqli->query("SELECT COUNT(*) as total_users FROM `{$prefix}users`");
        if ($result) {
            $row = $result->fetch_assoc();
            echo sprintf(t('test_total_users', 'Total users found: %d'), $row['total_users']);
        } else {
            echo t('test_db_query_failed', 'Database query failed.');
        }
    ?>
</p>
<p>
    <?php
        echo "<b>" . t('test_current_lang', 'Current Language Test:') . "</b><br>";
        echo sprintf(t('test_lang_is', 'The current language is: %s'), $_SESSION['current_language']);
    ?>
</p>

<hr>

<h3><?php echo t('ajax_test_title', 'AJAX Test'); ?></h3>
<p><?php echo t('ajax_test_desc', 'Click the button below to make an AJAX request to the server.'); ?></p>
<button id="ajax-test-button"><?php echo t('ajax_test_button', 'Fetch Server Time'); ?></button>

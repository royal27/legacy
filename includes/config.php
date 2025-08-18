<?php
// Configurare de bază: DB, multi-language, template, permalinks

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'legacy');
define('DB_PREFIX', 'cms_');

define('SITE_NAME', 'Legacy CMS');
define('SITE_URL', 'http://localhost/');

define('DEFAULT_LANG', 'ro');
define('DEFAULT_TEMPLATE', 'gradient');

$multiLanguages = ['ro', 'en', 'fr', 'de']; // Exemplu
$templates = ['gradient', 'dark', 'classic'];

function getDefaultLanguage() {
    return DEFAULT_LANG;
}

function getDefaultTemplate() {
    return DEFAULT_TEMPLATE;
}
?>
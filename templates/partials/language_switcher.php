<?php
if (!isset($db)) {
    return;
}

$active_languages = $db->query("SELECT name, code FROM languages WHERE is_active = 1 ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$current_lang_code = $_SESSION['lang'] ?? 'en';

if (count($active_languages) > 1):
?>
<div class="language-switcher">
    <div class="current-lang">
        <span><?php echo strtoupper($current_lang_code); ?></span>
    </div>
    <ul class="lang-dropdown">
        <?php foreach ($active_languages as $lang): ?>
            <li>
                <a href="<?php echo SITE_URL; ?>/language/<?php echo $lang['code']; ?>" class="<?php echo ($current_lang_code == $lang['code']) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($lang['name']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<style>
.language-switcher {
    position: relative;
    margin-left: 15px;
}
.current-lang {
    cursor: pointer;
    background: var(--color-secondary);
    color: white;
    padding: 5px 10px;
    border-radius: 3px;
    font-weight: bold;
}
.language-switcher > .lang-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    list-style: none;
    padding: 0;
    margin: 5px 0 0 0;
    border-radius: 3px;
    z-index: 100;
    min-width: 120px;
}
.language-switcher:hover > .lang-dropdown {
    display: block;
}
.lang-dropdown li a {
    display: block;
    padding: 8px 12px;
    color: var(--color-dark) !important; /* Override header link color */
    text-decoration: none;
    font-weight: normal !important;
}
.lang-dropdown li a:hover {
    background-color: var(--color-light-gray);
    color: var(--color-primary) !important;
}
.lang-dropdown li a.active {
    font-weight: bold !important;
    background-color: var(--color-primary);
    color: white !important;
}
</style>

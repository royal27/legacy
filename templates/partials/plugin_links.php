<?php
// This partial is included from a template file, so we can use the global $db object.
if (!isset($db)) {
    return;
}

$active_plugins = $db->query("SELECT name, custom_link FROM plugins WHERE is_active = 1 AND custom_link IS NOT NULL AND custom_link != ''")->fetch_all(MYSQLI_ASSOC);

if (!empty($active_plugins)):
?>
<div class="plugin-links-container">
    <h3>Our Features</h3>
    <ul class="plugin-links-list">
        <?php foreach ($active_plugins as $plugin): ?>
            <li>
                <a href="<?php echo htmlspecialchars($plugin['custom_link']); ?>" target="_blank">
                    <?php echo htmlspecialchars($plugin['name']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<style>
.plugin-links-container {
    margin: 20px 0;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    border: 1px solid #eee;
}
.plugin-links-container h3 {
    margin-top: 0;
    color: var(--color-secondary);
}
.plugin-links-list {
    list-style: none;
    padding: 0;
}
.plugin-links-list li a {
    display: block;
    padding: 8px 0;
    text-decoration: none;
    color: var(--color-primary);
    font-weight: bold;
    transition: color 0.3s;
}
.plugin-links-list li a:hover {
    color: var(--color-accent);
}
</style>

<header>
    <div class="header-content">
        <button id="menu-toggle" class="menu-toggle-btn">
            &#9776;
        </button>
        <h2><?php echo $page_title ?? 'Admin Panel'; ?></h2>
        <div class="admin-lang-switcher">
            <form>
                <select name="set_admin_lang" onchange="this.form.submit()">
                    <?php while($lang = $available_languages->fetch_assoc()): ?>
                        <option value="<?php echo $lang['code']; ?>" <?php echo ($lang['code'] === $admin_lang) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lang['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>
    </div>
</header>

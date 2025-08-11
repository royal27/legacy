<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['logo_text'] ?? 'Restaurant Menu'); ?></title>
    <link rel="stylesheet" href="templates/default/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="logo">
            <?php if (!empty($settings['logo_image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($settings['logo_image']); ?>" alt="Logo">
            <?php else: ?>
                <h1><?php echo htmlspecialchars($settings['logo_text']); ?></h1>
            <?php endif; ?>
        </div>
        <nav class="language-switcher">
            <?php foreach ($available_languages as $language): ?>
                <a href="?lang=<?php echo $language['code']; ?>" class="<?php echo ($lang === $language['code']) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($language['name']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </header>

    <main class="menu-grid">
        <?php if ($menu_items->num_rows > 0): ?>
            <?php while($item = $menu_items->fetch_assoc()): ?>
                <div class="menu-item">
                    <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="menu-item-content">
                        <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <span class="price">€<?php echo htmlspecialchars($item['price']); ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No menu items available in this language.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p><?php echo htmlspecialchars($settings['footer_text']); ?></p>
    </footer>

</body>
</html>

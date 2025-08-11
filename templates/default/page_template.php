<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page['title']); ?> - <?php echo htmlspecialchars($settings['logo_text'] ?? 'Restaurant Menu'); ?></title>
    <link rel="stylesheet" href="/templates/default/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="logo">
             <a href="/" style="text-decoration:none; color:white;">
                <?php if (!empty($settings['logo_image'])): ?>
                    <img src="/uploads/<?php echo htmlspecialchars($settings['logo_image']); ?>" alt="Logo">
                <?php else: ?>
                    <h1><?php echo htmlspecialchars($settings['logo_text']); ?></h1>
                <?php endif; ?>
            </a>
        </div>
        <nav class="language-switcher">
            <!-- No language switcher on generic pages -->
        </nav>
    </header>

    <main class="page-content" style="padding: 2rem; max-width: 900px; margin: 2rem auto; background: #fff; border-radius: 10px;">
        <h2><?php echo htmlspecialchars($page['title']); ?></h2>
        <div>
            <?php echo nl2br(htmlspecialchars($page['content'])); ?>
        </div>
    </main>

    <footer>
        <div class="footer-links">
            <?php if ($footer_pages->num_rows > 0): ?>
                <?php while($footer_page = $footer_pages->fetch_assoc()): ?>
                    <a href="/page/<?php echo htmlspecialchars($footer_page['slug']); ?>"><?php echo htmlspecialchars($footer_page['title']); ?></a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <p><?php echo htmlspecialchars($settings['footer_text']); ?></p>
    </footer>

</body>
</html>

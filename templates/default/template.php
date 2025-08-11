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
            <form>
                <select name="lang" onchange="window.location.href = '?lang=' + this.value;">
                    <?php foreach ($available_languages as $language): ?>
                        <option value="<?php echo $language['code']; ?>" <?php echo ($lang === $language['code']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($language['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
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

    <?php if ($gallery_images->num_rows > 0): ?>
    <section class="slideshow-container">
        <div class="slideshow">
            <?php while($img = $gallery_images->fetch_assoc()): ?>
                <div class="slide">
                    <img src="uploads/<?php echo htmlspecialchars($img['image_filename']); ?>" alt="Gallery image">
                </div>
            <?php endwhile; ?>
        </div>
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    </section>
    <?php endif; ?>

    <footer>
        <p><?php echo htmlspecialchars($settings['footer_text']); ?></p>
    </footer>

    <script>
        let slideIndex = 1;
        showSlides(slideIndex);

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("slide");
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[slideIndex-1].style.display = "block";
        }
    </script>
    <script>
        // Lightbox script
        document.addEventListener('DOMContentLoaded', (event) => {
            var modal = document.getElementById("lightbox-modal");
            var modalImg = document.getElementById("lightbox-image");
            var images = document.querySelectorAll(".slide img");
            images.forEach(img => {
                img.onclick = function(){
                    modal.style.display = "block";
                    modalImg.src = this.src;
                }
            });

            var span = document.getElementsByClassName("close-modal")[0];
            span.onclick = function() {
                modal.style.display = "none";
            }

            modal.onclick = function(e) {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            }
        });
    </script>

    <!-- The Lightbox Modal -->
    <div id="lightbox-modal" class="modal">
        <span class="close-modal">&times;</span>
        <img class="modal-content" id="lightbox-image">
    </div>
</body>
</html>

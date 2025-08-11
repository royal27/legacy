<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['logo_text'] ?? 'Restaurant Menu'); ?></title>
    <link rel="stylesheet" href="/templates/red_maple/style.css">
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
            <form>
                <select name="lang" onchange="window.location.href = '/?lang=' + this.value;">
                    <?php foreach ($available_languages as $language): ?>
                        <option value="<?php echo $language['code']; ?>" <?php echo ($lang === $language['code']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($language['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </nav>
    </header>

    <?php if ($offers->num_rows > 0): ?>
    <div class="offer-ticker">
        <div class="offer-text">
            <?php
                $offer_texts = [];
                while($offer = $offers->fetch_assoc()) {
                    $offer_texts[] = htmlspecialchars($offer['offer_text']);
                }
                echo implode(' *** ', $offer_texts);
            ?>
        </div>
    </div>
    <?php endif; ?>

    <main>
        <?php if ($menu_items->num_rows > 0): ?>
            <?php foreach ($menu_by_category as $category_name => $items): ?>
                <?php if (!empty($items)): ?>
                    <div class="category-section">
                        <h2 class="category-title"><?php echo htmlspecialchars($category_name); ?></h2>
                        <div class="menu-grid">
                            <?php foreach ($items as $item): ?>
                                <div class="menu-item">
                                    <img src="/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <div class="menu-item-content">
                                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                                        <span class="price">€<?php echo htmlspecialchars($item['price']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; padding: 2rem;">No menu items available in this language.</p>
        <?php endif; ?>
    </main>

    <?php if ($gallery_images->num_rows > 0): ?>
    <section class="slideshow-container">
        <div class="slideshow">
            <?php mysqli_data_seek($gallery_images, 0); ?>
            <?php while($item = $gallery_images->fetch_assoc()): ?>
                <div class="slide">
                    <?php if ($item['media_type'] === 'image'): ?>
                        <img src="/uploads/<?php echo htmlspecialchars($item['media_path']); ?>" alt="Gallery image">
                    <?php elseif ($item['media_type'] === 'video_upload'): ?>
                        <video controls src="/uploads/<?php echo htmlspecialchars($item['media_path']); ?>"></video>
                    <?php elseif ($item['media_type'] === 'video_embed'):
                        $embed_url = $item['media_path'];
                        if (strpos($embed_url, 'youtube.com/watch?v=') !== false) {
                            $video_id = substr($embed_url, strpos($embed_url, 'v=') + 2);
                            $embed_url = "https://www.youtube.com/embed/" . $video_id;
                        }
                    ?>
                        <iframe src="<?php echo htmlspecialchars($embed_url); ?>" frameborder="0" allowfullscreen></iframe>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    </section>
    <?php endif; ?>

    <footer>
        <div class="footer-links">
            <?php if ($footer_pages->num_rows > 0): ?>
                <?php while($page = $footer_pages->fetch_assoc()): ?>
                    <a href="/page/<?php echo htmlspecialchars($page['slug']); ?>"><?php echo htmlspecialchars($page['title']); ?></a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
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

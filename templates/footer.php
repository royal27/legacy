<?php
// Prevent direct file access
if (!defined('APP_LOADED')) {
    http_response_code(403);
    die('Forbidden');
}
?>
            </div> <!-- .container -->
        </main> <!-- .main-content -->

        <footer class="main-footer">
            <div class="footer-text">
                <?php echo !empty($settings['footer_text']) ? $settings['footer_text'] : '© ' . date('Y') . ' My Website. All rights reserved.'; ?>
            </div>
            <div class="footer-links">
                <?php
                $footer_menu_items = get_menu('footer_nav');
                foreach ($footer_menu_items as $item):
                    $url = (filter_var($item['url'], FILTER_VALIDATE_URL)) ? $item['url'] : SITE_URL . '/' . ltrim($item['url'], '/');
                ?>
                    <a href="<?php echo htmlspecialchars($url); ?>" target="<?php echo htmlspecialchars($item['target']); ?>">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </footer>
    </div> <!-- .page-wrapper -->

    <!-- Fade out script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const links = document.querySelectorAll('a:not([target="_blank"]):not([href^="#"])');

            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.href;
                    // Prevent navigation if it's the same page
                    if (href === window.location.href) {
                        e.preventDefault();
                        return;
                    }

                    // Don't fade for external links or special protocols
                    if (this.hostname !== window.location.hostname || this.protocol !== window.location.protocol) {
                        return;
                    }

                    e.preventDefault();
                    document.body.style.transition = 'opacity 0.5s ease-in-out';
                    document.body.style.opacity = '0';

                    setTimeout(() => {
                        window.location.href = href;
                    }, 500); // Match this duration with the CSS transition
                });
            });
        });
    </script>
</body>
</html>

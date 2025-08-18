</div> <!-- .container -->
        </main>
        <footer class="main-footer">
            <div class="container">
                <p><?php echo htmlspecialchars($site_settings['footer_text'] ?? '© ' . date('Y') . ' My Awesome Site'); ?></p>
                <div class="footer-links">
                    <?php
                    $footer_links = $site_settings['footer_links'] ?? [];
                    foreach ($footer_links as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo htmlspecialchars($link['text']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </footer>
    </div> <!-- .site-wrapper -->
    <script src="/js/main.js"></script>
</body>
</html>

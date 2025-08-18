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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="/js/main.js"></script>
    <script src="/js/auth.js"></script>
    <script src="/js/user_settings.js"></script>
    <script>
        // Configure Toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Fade-in effect
        window.addEventListener('load', () => {
            document.body.style.opacity = 1;
        });
    </script>
</body>
</html>

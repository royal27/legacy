<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}
?>
            </main> <!-- .admin-page-content -->
            <footer class="admin-footer">
                <p>&copy; <?php echo date('Y'); ?> - My Website Admin Panel. All Rights Reserved.</p>
            </footer>
        </div> <!-- .admin-main-content -->
    </div> <!-- .admin-wrapper -->

    <!-- Toastr JS -->
    <script src="<?php echo SITE_URL; ?>/admin/assets/js/toastr.min.js"></script>
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
        }

        <?php
        // Display flash messages
        if (isset($_SESSION['flash_message'])):
            $flash = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            ?>
            toastr.<?php echo $flash['type']; ?>('<?php echo addslashes($flash['message']); ?>');
        <?php endif; ?>
    </script>
</body>
</html>

</main> <!--- Closes the main tag from header.php -->

<footer>
    <p>&copy; <?php echo date('Y'); ?> <?php echo t('site_title', 'My Awesome Website'); ?>. <?php echo t('footer_rights', 'All rights reserved.'); ?></p>
</footer>

<!-- Scripts can be loaded here -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="/<?php echo $active_template_path; ?>/assets/js/main.js"></script>

</body>
</html>

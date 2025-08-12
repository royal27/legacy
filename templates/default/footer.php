</main> <!-- /.container -->

<footer class="main-footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> MySite. All Rights Reserved.</p>
        <?php Hooks::do_action('footer_content'); ?>
        <div class="language-switcher">
            <span>Switch Language:</span>
            <a href="/home/lang/en">English</a>
            <a href="/home/lang/ro">Română</a>
        </div>
        <div class="theme-switcher" style="margin-top: 10px;">
            <span>Switch Theme:</span>
            <button onclick="toggleTheme()">Toggle Dark Mode</button>
        </div>
    </div>
</footer>

<script src="/public/js/jquery.min.js"></script>
<script src="/public/js/toastr.min.js"></script>
<script>
// Simple theme switcher
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', newTheme);
    // You might want to save this preference in localStorage
    localStorage.setItem('theme', newTheme);
}

// Apply saved theme on page load
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
});
</script>

<?php Session::display_flash_messages(); ?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'My Awesome Project' ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>

    <?php
        $is_admin_area = (strpos($_SERVER['QUERY_STRING'], 'admin') === 0);
    ?>
    <div class="site-container">
        <?php if ($is_admin_area && \App\Core\Auth::check()): ?>
            <aside class="admin-sidebar">
                <h3>Admin Menu</h3>
                <nav>
                    <a href="/admin/dashboard">Dashboard</a>
                    <a href="/admin/users">Users</a>
                    <a href="/admin/roles">Roles</a>
                    <a href="/admin/plugins">Plugins</a>
                    <?php \App\Core\Hooks::trigger('admin_sidebar_links'); ?>
                    <hr>
                    <a href="/admin/settings">Site Settings</a>
                </nav>
                <hr>
                <a href="/">View Site</a>
            </aside>
        <?php endif; ?>

        <div class="main-content">
            <header>
                <!-- Main navigation will go here -->
                <a href="/">Home</a>
                <a href="/forum">Forum</a>
                <?php \App\Core\Hooks::trigger('main_nav_links'); ?>
                <?php if (\App\Core\Auth::check()): ?>
                    <a href="/admin">Admin</a>
                    <a href="/logout">Logout</a>
                <?php else: ?>
                    <a href="/login">Login</a>
                <?php endif; ?>
            </header>

            <main class="content-wrapper">
                <?= $content ?? '' ?>
            </main>

            <footer>
        <p>&copy; <?= date('Y') ?> My Awesome Project. All rights reserved.</p>
    </footer>
    </div>

     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
     <script>
        // Toastr notifications for flash messages
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };
        <?php $success = \App\Core\Session::getFlash('success'); ?>
        <?php if ($success): ?>
            toastr.success('<?= addslashes($success) ?>');
        <?php endif; ?>
        <?php $error = \App\Core\Session::getFlash('error'); ?>
        <?php if ($error): ?>
            toastr.error('<?= addslashes($error) ?>');
        <?php endif; ?>
     </script>

    <?php \App\Core\Hooks::trigger('site_footer'); ?>
</body>
</html>

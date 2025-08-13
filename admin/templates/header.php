<?php
if (!defined('ADMIN_AREA')) {
    http_response_code(403);
    die('Forbidden');
}
$base_url = rtrim(SITE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin Panel</title>

    <!-- jQuery & jQuery UI (must be loaded in the head) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- We can reuse the main stylesheet and add admin-specific overrides -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/admin/assets/css/admin-style.css">

    <!-- Toastr CSS for notifications (Embedded for debugging) -->
    <style>
    /*
 * Note that this is toastr v2.1.3, the "latest" version in url has no more main
tenance,
 * please go to https://cdnjs.com/libraries/toastr.js and pick a certain version
 you want to use,
 * make sure you copy the url from the website since the url may change between
versions.
 * */
.toast-title{font-weight:700}.toast-message{-ms-word-wrap:break-word;word-wrap:b
reak-word}.toast-message a,.toast-message label{color:#FFF}.toast-message a:hove
r{color:#CCC;text-decoration:none}.toast-close-button{position:relative;right:-.
3em;top:-.3em;float:right;font-size:20px;font-weight:700;color:#FFF;-webkit-text
-shadow:0 1px 0 #fff;text-shadow:0 1px 0 #fff;opacity:.8;-ms-filter:progid:DXIma
geTransform.Microsoft.Alpha(Opacity=80);filter:alpha(opacity=80);line-height:1}.
toast-close-button:focus,.toast-close-button:hover{color:#000;text-decoration:no
ne;cursor:pointer;opacity:.4;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(
Opacity=40);filter:alpha(opacity=40)}.rtl .toast-close-button{left:-.3em;float:l
eft;right:.3em}button.toast-close-button{padding:0;cursor:pointer;background:0 0
;border:0;-webkit-appearance:none}.toast-top-center{top:0;right:0;width:100%}.to
ast-bottom-center{bottom:0;right:0;width:100%}.toast-top-full-width{top:0;right:
0;width:100%}.toast-bottom-full-width{bottom:0;right:0;width:100%}.toast-top-lef
t{top:12px;left:12px}.toast-top-right{top:12px;right:12px}.toast-bottom-right{ri
ght:12px;bottom:12px}.toast-bottom-left{bottom:12px;left:12px}#toast-container{p
osition:fixed;z-index:999999;pointer-events:none}#toast-container *{-moz-box-siz
ing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}#toast-contai
ner>div{position:relative;pointer-events:auto;overflow:hidden;margin:0 0 6px;pad
ding:15px 15px 15px 50px;width:300px;-moz-border-radius:3px;-webkit-border-radiu
s:3px;border-radius:3px;background-position:15px center;background-repeat:no-rep
eat;-moz-box-shadow:0 0 12px #999;-webkit-box-shadow:0 0 12px #999;box-shadow:0
0 12px #999;color:#FFF;opacity:.8;-ms-filter:progid:DXImageTransform.Microsoft.A
lpha(Opacity=80);filter:alpha(opacity=80)}#toast-container>div.rtl{direction:rtl
;padding:15px 50px 15px 15px;background-position:right 15px center}#toast-contai
ner>div:hover{-moz-box-shadow:0 0 12px #000;-webkit-box-shadow:0 0 12px #000;box
-shadow:0 0 12px #000;opacity:1;-ms-filter:progid:DXImageTransform.Microsoft.Alp
ha(Opacity=100);filter:alpha(opacity=100);cursor:pointer}#toast-container>.toast
-info{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAA
YCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQ
AAAGwSURBVEhLtZa9SgNBEMc9sUxxRcoUKSzSWIhXpFMhhYWFhaBg4yPYiWCXZxBLERsLRS3EQkEfwCK
djWJAwSKCgoKCcudv4O5YLrt7EzgXhiU3/4+b2ckmwVjJSpKkQ6wAi4gwhT+z3wRBcEz0yjSseUTrcRy
fsHsXmD0AmbHOC9Ii8VImnuXBPglHpQ5wwSVM7sNnTG7Za4JwDdCjxyAiH3nyA2mtaTJufiDZ5dCaqlI
tILh1NHatfN5skvjx9Z38m69CgzuXmZgVrPIGE763Jx9qKsRozWYw6xOHdER+nn2KkO+Bb+UV5CBN6WC
6QtBgbRVozrahAbmm6HtUsgtPC19tFdxXZYBOfkbmFJ1VaHA1VAHjd0pp70oTZzvR+EVrx2Ygfdsq6eu
55BHYR8hlcki+n+kERUFG8BrA0BwjeAv2M8WLQBtcy+SD6fNsmnB3AlBLrgTtVW1c2QN4bVWLATaIS60
J2Du5y1TiJgjSBvFVZgTmwCU+dAZFoPxGEEs8nyHC9Bwe2GvEJv2WXZb0vjdyFT4Cxk3e/kIqlOGoVLw
wPevpYHT+00T+hWwXDf4AJAOUqWcDhbwAAAAASUVORK5CYII=)!important}#toast-container>.t
oast-error{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB
gAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAc
dvqGQAAAHOSURBVEhLrZa/SgNBEMZzh0WKCClSCKaIYOED+AAKeQQLG8HWztLCImBrYadgIdY+gIKNYk
BFSwu7CAoqCgkkoGBI/E28PdbLZmeDLgzZzcx83/zZ2SSXC1j9fr+I1Hq93g2yxH4iwM1vkoBWAdxCmp
zTxfkN2RcyZNaHFIkSo10+8kgxkXIURV5HGxTmFuc75B2RfQkpxHG8aAgaAFa0tAHqYFfQ7Iwe2yhODk
8+J4C7yAoRTWI3w/4klGRgR4lO7Rpn9+gvMyWp+uxFh8+H+ARlgN1nJuJuQAYvNkEnwGFck18Er4q3eg
Ec/oO+mhLdKgRyhdNFiacC0rlOCbhNVz4H9FnAYgDBvU3QIioZlJFLJtsoHYRDfiZoUyIxqCtRpVlANq
0EU4dApjrtgezPFad5S19Wgjkc0hNVnuF4HjVA6C7QrSIbylB+oZe3aHgBsqlNqKYH48jXyJKMuAbiyV
J8KzaB3eRc0pg9VwQ4niFryI68qiOi3AbjwdsfnAtk0bCjTLJKr6mrD9g8iq/S/B81hguOMlQTnVyG40
wAcjnmgsCNESDrjme7wfftP4P7SP4N3CJZdvzoNyGq2c/HWOXJGsvVg+RA/k2MC/wN6I2YA2Pt8GkAAA
AASUVORK5CYII=)!important}#toast-container>.toast-success{background-image:url(d
ata:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4
c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAADsSURBVEhLY2AYBfQMgf///3P
8+/evAIgvA/FsIF+BavYDDWMBGroaSMMBiE8VC7AZDrIFaMFnii3AZTjUgsUUWUDA8OdAH6iQbQEhw4H
yGsPEcKBXBIC4ARhex4G4BsjmweU1soIFaGg/WtoFZRIZdEvIMhxkCCjXIVsATV6gFGACs4Rsw0EGgII
H3QJYJgHSARQZDrWAB+jawzgs+Q2UO49D7jnRSRGoEFRILcdmEMWGI0cm0JJ2QpYA1RDvcmzJEWhABhD
/pqrL0S0CWuABKgnRki9lLseS7g2AlqwHWQSKH4oKLrILpRGhEQCw2LiRUIa4lwAAAABJRU5ErkJggg=
=)!important}#toast-container>.toast-warning{background-image:url(data:image/png
;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1B
AACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAGYSURBVEhL5ZSvTsNQFMbXZGICMYGYmJhAQIJA
ICYQPAACiSDB8AiICQQJT4CqQEwgJvYASAQCiZiYmJhAIBATCARJy+9rTsldd8sKu1M0+dLb057v6/lb
q/2rK0mS/TRNj9cWNAKPYIJII7gIxCcQ51cvqID+GIEX8ASG4B1bK5gIZFeQfoJdEXOfgX4QAQg7kH2A
65yQ87lyxb27sggkAzAuFhbbg1K2kgCkB1bVwyIR9m2L7PRPIhDUIXgGtyKw575yz3lTNs6X4JXnjV+L
KM/m3MydnTbtOKIjtz6VhCBq4vSm3ncdrD2lk0VgUXSVKjVDJXJzijW1RQdsU7F77He8u68koNZTz8Oz
5yGa6J3H3lZ0xYgXBK2QymlWWA+RWnYhskLBv2vmE+hBMCtbA7KX5drWyRT/2JsqZ2IvfB9Y4bWDNMFb
JRFmC9E74SoS0CqulwjkC0+5bpcV1CZ8NMej4pjy0U+doDQsGyo1hzVJttIjhQ7GnBtRFN1UarUlH8F3
xict+HY07rEzoUGPlWcjRFRr4/gChZgc3ZL2d8oAAAAASUVORK5CYII=)!important}#toast-conta
iner.toast-bottom-center>div,#toast-container.toast-top-center>div{width:300px;m
argin-left:auto;margin-right:auto}#toast-container.toast-bottom-full-width>div,#
toast-container.toast-top-full-width>div{width:96%;margin-left:auto;margin-right
:auto}.toast{background-color:#030303}.toast-success{background-color:#51A351}.t
oast-error{background-color:#BD362F}.toast-info{background-color:#2F96B4}.toast-
warning{background-color:#F89406}.toast-progress{position:absolute;left:0;bottom
:0;height:4px;background-color:#000;opacity:.4;-ms-filter:progid:DXImageTransfor
m.Microsoft.Alpha(Opacity=40);filter:alpha(opacity=40)}@media all and (max-width
:240px){#toast-container>div{padding:8px 8px 8px 50px;width:11em}#toast-containe
r>div.rtl{padding:8px 50px 8px 8px}#toast-container .toast-close-button{right:-.
2em;top:-.2em}#toast-container .rtl .toast-close-button{left:-.2em;right:.2em}}@
media all and (min-width:241px) and (max-width:480px){#toast-container>div{paddi
ng:8px 8px 8px 50px;width:18em}#toast-container>div.rtl{padding:8px 50px 8px 8px
}#toast-container .toast-close-button{right:-.2em;top:-.2em}#toast-container .rt
l .toast-close-button{left:-.2em;right:.2em}}@media all and (min-width:481px) an
d (max-width:768px){#toast-container>div{padding:15px 15px 15px 50px;width:25em}
#toast-container>div.rtl{padding:15px 50px 15px 15px}}
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="<?php echo $base_url; ?>/admin/index.php" class="logo-text">Admin</a>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=dashboard">Dashboard</a>
                    </li>
                    <li class="<?php echo ($page === 'settings') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=settings">Site Settings</a>
                    </li>
                    <li class="<?php echo ($page === 'users' || $page === 'edit_user') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=users">Users</a>
                    </li>
                    <li class="<?php echo ($page === 'roles' || $page === 'permissions') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=roles">Roles & Permissions</a>
                    </li>
                     <li class="<?php echo ($page === 'languages' || $page === 'translations') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=languages">Languages</a>
                    </li>
                    <li class="<?php echo ($page === 'plugins') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=plugins">Plugins</a>
                    </li>
                    <li class="<?php echo ($page === 'points') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=points">Points System</a>
                    </li>
                    <li class="<?php echo ($page === 'security') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=security">Security</a>
                    </li>

                    <li class="nav-heading">Appearance</li>
                    <li class="<?php echo ($page === 'menus') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=menus">Menus</a>
                    </li>
                    <li class="<?php echo ($page === 'pages' || $page === 'edit_page') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=pages">Pages</a>
                    </li>
                     <li class="<?php echo ($page === 'themes') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php?page=themes">Themes</a>
                    </li>

                    <li>
                        <a href="<?php echo $base_url; ?>/" target="_blank">View Site</a>
                    </li>
                </ul>
            </nav>
        </aside>
        <div class="admin-main-content">
            <header class="admin-top-header">
                <div class="welcome-message">
                    Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!
                </div>
                <div class="header-actions">
                    <a href="<?php echo $base_url; ?>/admin/logout.php" class="btn btn-accent">Logout</a>
                </div>
            </header>
            <main class="admin-page-content">
                <h1><?php echo htmlspecialchars($page_title); ?></h1>
                <hr>

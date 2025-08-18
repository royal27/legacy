<?php
// Router simplu pentru permalinks

function handleRoute() {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    switch ($uri) {
        case '':
        case 'dashboard':
            require_once __DIR__ . '/../dashboard/index.php';
            break;
        case 'admin':
            require_once __DIR__ . '/../admin/index.php';
            break;
        case 'install':
            require_once __DIR__ . '/../install/index.php';
            break;
        case 'chatroom':
            require_once __DIR__ . '/../plugins/chat/index.php';
            break;
        default:
            require_once __DIR__ . '/../404.php';
            break;
    }
}
?>
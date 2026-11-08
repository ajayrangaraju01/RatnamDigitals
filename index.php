<?php
require __DIR__ . '/includes/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = $uri === '' ? '/' : $uri;

switch ($uri) {
    case '/':
        $page = 'home';
        break;
    case '/portfolio':
        $page = 'portfolio';
        break;
    case '/services':
        $page = 'services';
        break;
    case '/book':
    case '/booking':
        $page = 'booking';
        break;
    case '/contact':
        $page = 'contact';
        break;
    default:
        $page = '404';
        break;
}

ob_start();
include __DIR__ . '/views/' . $page . '.php';
$content = ob_get_clean();

include __DIR__ . '/views/layout.php';

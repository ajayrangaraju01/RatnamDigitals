<?php
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = $uri === '' ? '/' : $uri;

if (str_starts_with($uri, '/admin')) {
    require __DIR__ . '/admin/index.php';
    exit;
}

require __DIR__ . '/index.php';

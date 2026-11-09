<?php
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = $uri === '' ? '/' : $uri;

$assetRoots = ['/static/', '/media/', '/uploads/'];
foreach ($assetRoots as $root) {
    if (str_starts_with($uri, $root)) {
        $filePath = __DIR__ . $uri;
        $normalized = str_replace('\\', '/', $filePath);
        $normalized = str_replace('/./', '/', $normalized);

        if (is_file($normalized) && realpath($normalized) !== false) {
            $extension = strtolower(pathinfo($normalized, PATHINFO_EXTENSION));
            $mimes = [
                'css' => 'text/css; charset=UTF-8',
                'js' => 'application/javascript; charset=UTF-8',
                'json' => 'application/json; charset=UTF-8',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp',
                'ico' => 'image/x-icon',
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'mov' => 'video/quicktime',
                'txt' => 'text/plain; charset=UTF-8',
            ];

            header('Content-Type: ' . ($mimes[$extension] ?? 'application/octet-stream'));
            readfile($normalized);
            exit;
        }
    }
}

if (str_starts_with($uri, '/admin')) {
    require __DIR__ . '/admin/index.php';
    exit;
}

require __DIR__ . '/index.php';

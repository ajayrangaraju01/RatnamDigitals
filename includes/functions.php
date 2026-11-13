<?php

function getDb(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    return $pdo;
}

function ensureAppDatabase(): void
{
    $pdo = getDb();

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS images (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            file TEXT NOT NULL,
            category TEXT DEFAULT 'other',
            caption TEXT DEFAULT '',
            is_featured INTEGER DEFAULT 0,
            carousel_crop_x REAL DEFAULT 50,
            carousel_crop_y REAL DEFAULT 50,
            uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )"
    );

    $imageColumns = $pdo->query('PRAGMA table_info(images)')->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('carousel_crop_x', $imageColumns, true)) {
        $pdo->exec('ALTER TABLE images ADD COLUMN carousel_crop_x REAL DEFAULT 50');
    }
    if (!in_array('carousel_crop_y', $imageColumns, true)) {
        $pdo->exec('ALTER TABLE images ADD COLUMN carousel_crop_y REAL DEFAULT 50');
    }

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS booking_requests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            client_name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT DEFAULT '',
            service_type TEXT NOT NULL,
            requested_date TEXT NOT NULL,
            requested_time TEXT DEFAULT '',
            notes TEXT DEFAULT '',
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS services (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            price TEXT NOT NULL,
            service_icon TEXT DEFAULT '',
            background_image TEXT DEFAULT '',
            service_crop_x REAL DEFAULT 50,
            service_crop_y REAL DEFAULT 50,
            order_index INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )"
    );

    $serviceColumns = $pdo->query('PRAGMA table_info(services)')->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('service_icon', $serviceColumns, true)) {
        $pdo->exec("ALTER TABLE services ADD COLUMN service_icon TEXT DEFAULT ''");
    }
    if (!in_array('service_crop_x', $serviceColumns, true)) {
        $pdo->exec('ALTER TABLE services ADD COLUMN service_crop_x REAL DEFAULT 50');
    }
    if (!in_array('service_crop_y', $serviceColumns, true)) {
        $pdo->exec('ALTER TABLE services ADD COLUMN service_crop_y REAL DEFAULT 50');
    }

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS hero_slides (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            subtitle TEXT DEFAULT '',
            media_file TEXT DEFAULT '',
            overlay_opacity INTEGER DEFAULT 50,
            button_text TEXT DEFAULT 'Book a Session',
            button_link TEXT DEFAULT '/book',
            order_index INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )"
    );

    $packagesTableExists = (bool) $pdo->query("SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = 'packages'")->fetchColumn();
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS packages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            price TEXT NOT NULL,
            description TEXT NOT NULL DEFAULT '',
            items TEXT NOT NULL DEFAULT '',
            is_featured INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            order_index INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )"
    );

    if (!$packagesTableExists) {
        $defaultPackages = [
            [
                'Signature Portrait',
                '₹12,000',
                'Perfect for personal branding, lifestyle sessions, or modern portraits.',
                "1 hour session\nProfessional editing\n20 final images",
                0,
                0,
            ],
            [
                'Wedding Story',
                '₹35,000',
                'A complete documentary-style coverage designed around your celebration.',
                "8 hours coverage\nCandid + portrait coverage\nHigh-resolution gallery",
                1,
                1,
            ],
            [
                'Brand Content',
                '₹22,000',
                'Creative, polished imagery for founders, businesses, and campaigns.',
                "Creative direction\nStudio or location shoot\nSocial-ready export set",
                0,
                2,
            ],
        ];
        $insertPackage = $pdo->prepare('INSERT INTO packages (name, price, description, items, is_featured, order_index) VALUES (:name, :price, :description, :items, :is_featured, :order_index)');
        foreach ($defaultPackages as [$name, $price, $description, $items, $featured, $order]) {
            $insertPackage->execute([
                ':name' => $name,
                ':price' => $price,
                ':description' => $description,
                ':items' => $items,
                ':is_featured' => $featured,
                ':order_index' => $order,
            ]);
        }
    }
}

function addFlash(string $type, string $message): void
{
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }

    $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
}

function getFlashMessages(): array
{
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function assetUrl(string $path): string
{
    $path = ltrim($path, '/');
    if ($path === '') {
        return '/';
    }
    return '/' . $path;
}

function ensureDirectories(string $relativePath): string
{
    $fullPath = APP_ROOT . '/' . ltrim($relativePath, '/');
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0777, true);
    }
    return $fullPath;
}

function saveUploadedFile(array $file, string $targetDir): ?string
{
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safeBase = preg_replace('/[^A-Za-z0-9_-]/', '_', $baseName);
    $safeName = ($safeBase ?: 'upload') . '_' . time() . '.' . $extension;

    $directory = ensureDirectories($targetDir);
    $destination = $directory . '/' . $safeName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ltrim(str_replace(APP_ROOT . '/', '', $destination), '/');
    }

    return null;
}

function isAdminAuthenticated(): bool
{
    return !empty($_SESSION['admin_authenticated']);
}

function validateAdminLogin(string $username, string $password): bool
{
    $expectedUser = getenv('ADMIN_USER') ?: 'admin';
    $expectedPass = getenv('ADMIN_PASS') ?: 'studio2026';

    return $username === $expectedUser && $password === $expectedPass;
}

function getServiceChoices(): array
{
    return [
        'portrait' => 'Portrait Session',
        'wedding' => 'Wedding Photography',
        'event' => 'Event Coverage',
        'studio' => 'Studio Shoot',
        'commercial' => 'Commercial / Product',
    ];
}

function getImageCategories(): array
{
    return [
        'wedding' => 'Wedding',
        'portrait' => 'Portrait',
        'event' => 'Event',
        'studio' => 'Studio',
        'commercial' => 'Commercial',
        'other' => 'Other',
    ];
}

function getFeaturedImages(): array
{
    $pdo = getDb();
    $featured = $pdo->query('SELECT * FROM images WHERE is_featured = 1 ORDER BY id DESC LIMIT 5')->fetchAll();
    if (count($featured) >= 2) {
        return $featured;
    }

    $images = [];
    foreach ($featured as $image) {
        $images[$image['id']] = $image;
    }
    $recent = $pdo->query('SELECT * FROM images ORDER BY uploaded_at DESC, id DESC LIMIT 5')->fetchAll();
    foreach ($recent as $image) {
        $images[$image['id']] = $image;
        if (count($images) >= 5) {
            break;
        }
    }

    return array_values($images);
}

function getHeroSlides(): array
{
    $pdo = getDb();
    $stmt = $pdo->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY order_index ASC, id DESC");
    return $stmt->fetchAll();
}

function getActiveServices(): array
{
    $pdo = getDb();
    $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY order_index ASC, id DESC");
    return $stmt->fetchAll();
}

function getActivePackages(): array
{
    $pdo = getDb();
    $packages = $pdo->query('SELECT * FROM packages WHERE is_active = 1 ORDER BY order_index ASC, id ASC')->fetchAll();
    foreach ($packages as &$package) {
        $package['items'] = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", '', $package['items']))), static fn(string $item): bool => $item !== ''));
    }
    unset($package);
    return $packages;
}

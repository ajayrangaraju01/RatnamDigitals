<?php
$flashMessages = getFlashMessages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Ratnam Digitals') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body class="<?= parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) === '/' ? 'home-page' : '' ?>">
    <nav class="navbar">
        <a href="/" class="logo">
            <img src="/static/images/logo.jpg" alt="Ratnam Digitals" class="logo-img">
            <span>Ratnam Digitals</span>
        </a>
        <button class="nav-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="primary-navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="nav-links" id="primary-navigation">
            <a href="/">Home</a>
            <a href="/portfolio">Portfolio</a>
            <a href="/services">Services</a>
            <a href="/book">Book Now</a>
            <a href="/contact">Contact</a>
        </div>
    </nav>

    <main>
        <?php foreach ($flashMessages as $flash): ?>
            <div class="messages">
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
            </div>
        <?php endforeach; ?>

        <?= $content ?>
    </main>

    <footer>
        <p>&copy; 2026 Ratnam Digitals. All rights reserved.</p>
    </footer>
    <script src="/static/js/mobile-nav.js"></script>
</body>
</html>

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
<body>
    <nav class="navbar">
        <a href="/" class="logo">
            <img src="/static/images/logo.jpg" alt="Ratnam Digitals" class="logo-img">
            <span>Ratnam Digitals</span>
        </a>
        <div class="nav-links">
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
</body>
</html>

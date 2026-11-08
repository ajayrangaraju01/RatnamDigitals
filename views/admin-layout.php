<?php $flashMessages = getFlashMessages(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
    <style>
        .admin-shell { max-width: 1200px; margin: 0 auto; }
        .admin-nav { display: flex; gap: 1rem; flex-wrap: wrap; margin: 1.5rem 0; }
        .admin-nav a { padding: 0.65rem 1rem; background: #111; color: #fff; border-radius: 6px; }
        .admin-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-top: 1rem; }
        .admin-card { background: #f8f8f8; border: 1px solid #eee; border-radius: 12px; padding: 1.25rem; }
        .admin-card h3 { margin-bottom: 0.6rem; }
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 0.7rem; text-align: left; }
        .admin-form { max-width: 700px; background: #f9f9f9; border: 1px solid #eee; padding: 1.5rem; border-radius: 12px; }
        .admin-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.75rem; }
        .mini-btn { padding: 0.5rem 0.9rem; background: #111; color: #fff; border-radius: 6px; border: none; cursor: pointer; }
        .mini-btn.secondary { background: #c8a97e; }
        .status-badge { padding: 0.35rem 0.65rem; border-radius: 999px; font-size: 0.8rem; background: #eaeaea; }
    </style>
</head>
<body>
    <main class="admin-shell">
        <?php foreach ($flashMessages as $flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endforeach; ?>

        <?php if (isAdminAuthenticated()): ?>
            <nav class="admin-nav">
                <a href="/admin?page=dashboard">Dashboard</a>
                <a href="/admin?page=upload">Upload</a>
                <a href="/admin?page=bookings">Bookings</a>
                <a href="/admin?page=services">Services</a>
                <a href="/admin?page=hero">Hero</a>
                <a href="/admin?page=logout">Logout</a>
            </nav>
        <?php endif; ?>

        <?= $content ?>
    </main>
</body>
</html>

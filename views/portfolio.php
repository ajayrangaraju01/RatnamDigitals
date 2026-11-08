<?php
$title = 'Portfolio - Ratnam Digitals';
$category = $_GET['category'] ?? 'all';
$pdo = getDb();

if ($category === 'all') {
    $images = $pdo->query("SELECT * FROM images ORDER BY uploaded_at DESC")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT * FROM images WHERE category = :category ORDER BY uploaded_at DESC");
    $stmt->execute([':category' => $category]);
    $images = $stmt->fetchAll();
}

$categories = getImageCategories();
?>

<h1 class="page-title">Portfolio</h1>
<div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 2rem;">
    <a class="btn-outline <?= $category === 'all' ? 'active' : '' ?>" href="/portfolio?category=all">All</a>
    <?php foreach ($categories as $key => $label): ?>
        <a class="btn-outline <?= $category === $key ? 'active' : '' ?>" href="/portfolio?category=<?= urlencode($key) ?>"><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
</div>

<?php if (!empty($images)): ?>
    <div class="portfolio-grid">
        <?php foreach ($images as $image): ?>
            <div class="portfolio-item">
                <img src="/<?= htmlspecialchars($image['file']) ?>" alt="<?= htmlspecialchars($image['title'] ?: 'Portfolio image') ?>">
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="section-subtitle">No images found in this category yet.</p>
<?php endif; ?>

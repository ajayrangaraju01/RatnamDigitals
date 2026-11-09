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
<div class="portfolio-filter-bar">
    <a class="btn-outline <?= $category === 'all' ? 'active' : '' ?>" href="/portfolio?category=all">All</a>
    <?php foreach ($categories as $key => $label): ?>
        <a class="btn-outline <?= $category === $key ? 'active' : '' ?>" href="/portfolio?category=<?= urlencode($key) ?>"><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
</div>

<?php if (!empty($images)): ?>
    <div class="portfolio-grid">
        <?php foreach ($images as $image): ?>
            <button type="button" class="portfolio-item" data-title="<?= htmlspecialchars($image['title'] ?: 'Ratnam Digitals') ?>" data-caption="<?= htmlspecialchars($image['caption'] ?: 'Curated photography by Ratnam Digitals.') ?>" data-image="/<?= htmlspecialchars($image['file']) ?>">
                <img src="/<?= htmlspecialchars($image['file']) ?>" alt="<?= htmlspecialchars($image['title'] ?: 'Portfolio image') ?>">
                <span class="portfolio-item-overlay">
                    <span>View Details</span>
                </span>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="portfolio-lightbox" id="portfolio-lightbox" aria-hidden="true">
        <button type="button" class="portfolio-lightbox-close" aria-label="Close gallery">&times;</button>
        <div class="portfolio-lightbox-inner">
            <img id="portfolio-lightbox-image" src="" alt="">
            <div class="portfolio-lightbox-copy">
                <span class="section-kicker">Portfolio</span>
                <h3 id="portfolio-lightbox-title">Ratnam Digitals</h3>
                <p id="portfolio-lightbox-caption">Curated photography by Ratnam Digitals.</p>
                <a href="/book" class="btn btn-primary">Book a session</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <p class="section-subtitle">No images found in this category yet.</p>
<?php endif; ?>

<script src="/static/js/portfolio-gallery.js"></script>

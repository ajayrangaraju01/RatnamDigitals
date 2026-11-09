<?php
$title = 'Ratnam Digitals - Professional Photography Studio';
$heroSlides = getHeroSlides();
$featuredImages = getFeaturedImages();
$services = getActiveServices();
$highlightCards = [
    [
        'category' => 'portrait',
        'eyebrow' => 'Portraits',
        'title' => 'Editorial Portraits',
        'description' => 'Natural, confident visuals made around you.',
    ],
    [
        'category' => 'wedding',
        'eyebrow' => 'Weddings',
        'title' => 'Wedding Stories',
        'description' => 'Honest moments, thoughtfully photographed.',
    ],
    [
        'category' => 'commercial',
        'eyebrow' => 'Commercial',
        'title' => 'Brand Content',
        'description' => 'Distinctive images for the work you put into the world.',
    ],
];
$categoryImageQuery = getDb()->prepare('SELECT * FROM images WHERE category = :category ORDER BY is_featured DESC, uploaded_at DESC, id DESC LIMIT 1');
foreach ($highlightCards as $index => $card) {
    $categoryImageQuery->execute([':category' => $card['category']]);
    $image = $categoryImageQuery->fetch();
    if ($image) {
        $highlightCards[$index]['image'] = $image;
        $highlightCards[$index]['href'] = '/portfolio?category=' . urlencode($card['category']);
    } else {
        unset($highlightCards[$index]);
    }
}
$highlightCards = array_values($highlightCards);
?>

<?php if (!empty($featuredImages)): ?>
<section class="content-section home-photo-section">
    <div class="carousel" aria-label="Recent photography">
        <div class="carousel-track">
        <?php foreach ($featuredImages as $image): ?>
            <div class="carousel-slide">
                <img src="/<?= htmlspecialchars($image['file']) ?>" alt="<?= htmlspecialchars($image['title'] ?: 'Ratnam Digitals photography') ?>" style="object-position: <?= number_format((float) ($image['carousel_crop_x'] ?? 50), 2, '.', '') ?>% <?= number_format((float) ($image['carousel_crop_y'] ?? 50), 2, '.', '') ?>%;" loading="lazy">
                <div class="caption">
                    <div>
                        <h3><?= htmlspecialchars($image['title'] ?: 'Ratnam Digitals') ?></h3>
                        <?php if (!empty($image['caption'])): ?><span><?= htmlspecialchars($image['caption']) ?></span><?php endif; ?>
                    </div>
                    <a href="/book" class="btn btn-primary">Book a Session</a>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php if (count($featuredImages) > 1): ?>
            <button class="carousel-btn prev" type="button" aria-label="Previous photo">&#10094;</button>
            <button class="carousel-btn next" type="button" aria-label="Next photo">&#10095;</button>
            <div class="carousel-dots" aria-label="Choose a photo"></div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($heroSlides)): ?>
<div class="hero-carousel">
    <div class="hero-carousel-track">
        <?php foreach ($heroSlides as $slide): ?>
            <div class="hero-carousel-slide">
                <?php if (!empty($slide['media_file'])): ?>
                    <?php if (preg_match('/\.(mp4|webm|mov)$/i', $slide['media_file'])): ?>
                        <video class="hero-media" src="/<?= htmlspecialchars($slide['media_file']) ?>" muted autoplay loop playsinline></video>
                    <?php else: ?>
                        <img class="hero-media" src="/<?= htmlspecialchars($slide['media_file']) ?>" alt="<?= htmlspecialchars($slide['title']) ?>">
                    <?php endif; ?>
                <?php else: ?>
                    <div class="hero-media hero-media-fallback"></div>
                <?php endif; ?>
                <div class="hero-overlay" style="background: rgba(0,0,0,0.42);"></div>
                <div class="hero-content">
                    <span class="eyebrow">Timeless photography</span>
                    <h1><?= htmlspecialchars($slide['title']) ?></h1>
                    <?php if (!empty($slide['subtitle'])): ?><p><?= htmlspecialchars($slide['subtitle']) ?></p><?php endif; ?>
                    <?php if (!empty($slide['button_text'])): ?>
                        <a href="<?= htmlspecialchars($slide['button_link']) ?: '/book' ?>" class="btn btn-primary"><?= htmlspecialchars($slide['button_text']) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($heroSlides) > 1): ?>
        <button class="hero-carousel-btn hero-prev" aria-label="Previous slide">&#10094;</button>
        <button class="hero-carousel-btn hero-next" aria-label="Next slide">&#10095;</button>
        <div class="hero-carousel-dots"></div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (!empty($highlightCards)): ?>
<section class="feature-strip" aria-label="Explore photography">
    <?php foreach ($highlightCards as $card): ?>
        <a class="feature-card" href="<?= htmlspecialchars($card['href']) ?>">
            <img src="/<?= htmlspecialchars($card['image']['file']) ?>" alt="<?= htmlspecialchars($card['title']) ?>" loading="lazy">
            <div class="feature-card-content">
                <span class="feature-card-kicker"><?= htmlspecialchars($card['eyebrow']) ?></span>
                <h3><?= htmlspecialchars($card['title']) ?></h3>
                <p><?= htmlspecialchars($card['description']) ?></p>
                <span class="feature-card-action">View gallery <span aria-hidden="true">&#8599;</span></span>
            </div>
        </a>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<section class="story-section">
    <div class="story-panel">
        <div>
            <span class="section-kicker">Why choose us</span>
            <h2 class="section-title left-aligned">Crafted imagery, tailored to your vision.</h2>
        </div>
        <p>At Ratnam Digitals, we create striking portraits, events, and commercial visuals with a refined editorial eye. Every frame is thoughtfully composed to feel authentic, elevated, and memorable.</p>
    </div>
    <div class="stats-grid">
        <div class="stat-box">
            <strong>500+</strong>
            <span>Sessions captured</span>
        </div>
        <div class="stat-box">
            <strong>8+</strong>
            <span>Years of experience</span>
        </div>
        <div class="stat-box">
            <strong>100%</strong>
            <span>Client-focused</span>
        </div>
    </div>
</section>

<?php if (!empty($services)): ?>
<section class="content-section">
    <h2 class="section-title">Our Services</h2>
    <p class="section-subtitle">Professional photography for every occasion</p>
    <div class="services-grid">
        <?php foreach ($services as $service): ?>
            <div class="service-card<?= !empty($service['background_image']) ? ' service-card-bg' : '' ?>" <?php if (!empty($service['background_image'])): ?>style="background-image: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.7)), url('/<?= htmlspecialchars($service['background_image']) ?>');"<?php endif; ?>>
                <?php if (!empty($service['service_icon'])): ?><span class="feature-icon" aria-hidden="true"><?= htmlspecialchars($service['service_icon']) ?></span><?php endif; ?>
                <h3><?= htmlspecialchars($service['title']) ?></h3>
                <p><?= htmlspecialchars($service['description']) ?></p>
                <div class="price"><?= htmlspecialchars($service['price']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="cta-banner">
    <div>
        <span class="section-kicker">Let’s create something beautiful</span>
        <h2>Ready to Create Something Beautiful?</h2>
    </div>
    <a href="/book" class="btn btn-primary">Get Started</a>
</section>

<script src="/static/js/hero-carousel.js"></script>
<script src="/static/js/carousel.js"></script>

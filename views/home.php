<?php
$title = 'Ratnam Digitals - Professional Photography Studio';
$heroSlides = getHeroSlides();
$featuredImages = getFeaturedImages();
$services = getActiveServices();
?>

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
                <div class="hero-overlay" style="background: rgba(0,0,0,0.5);"></div>
                <div class="hero-content">
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

<?php if (!empty($featuredImages)): ?>
<section style="margin-bottom: 3rem;">
    <h2 class="section-title">Featured Work</h2>
    <p class="section-subtitle">A glimpse into our portfolio</p>
    <div class="portfolio-grid">
        <?php foreach ($featuredImages as $image): ?>
            <div class="portfolio-item">
                <img src="/<?= htmlspecialchars($image['file']) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($services)): ?>
<section style="margin-bottom: 3rem;">
    <h2 class="section-title">Our Services</h2>
    <p class="section-subtitle">Professional photography for every occasion</p>
    <div class="services-grid">
        <?php foreach ($services as $service): ?>
            <div class="service-card<?= !empty($service['background_image']) ? ' service-card-bg' : '' ?>" <?php if (!empty($service['background_image'])): ?>style="background-image: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.7)), url('/<?= htmlspecialchars($service['background_image']) ?>');"<?php endif; ?>>
                <h3><?= htmlspecialchars($service['title']) ?></h3>
                <p><?= htmlspecialchars($service['description']) ?></p>
                <div class="price"><?= htmlspecialchars($service['price']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section style="text-align: center; padding: 3rem 0;">
    <h2 class="section-title">Ready to Create Something Beautiful?</h2>
    <p class="section-subtitle">Let's discuss your vision and bring it to life</p>
    <a href="/book" class="btn btn-primary">Get Started</a>
</section>

<script src="/static/js/hero-carousel.js"></script>

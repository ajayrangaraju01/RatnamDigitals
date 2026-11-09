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

$processSteps = [
    [
        'step' => '01',
        'title' => 'Discovery',
        'description' => 'We learn your style, goals, and the story you want to tell before the shoot begins.',
    ],
    [
        'step' => '02',
        'title' => 'Shoot Experience',
        'description' => 'Every session is guided to feel natural, relaxed, and beautifully directed.',
    ],
    [
        'step' => '03',
        'title' => 'Curated Delivery',
        'description' => 'Your final gallery is carefully edited and delivered with the quality your brand deserves.',
    ],
];

$testimonials = [
    [
        'name' => 'Ananya & Karthik',
        'role' => 'Wedding clients',
        'quote' => 'The photos felt timeless and honest. Every frame captured the emotion of the day without feeling staged.',
    ],
    [
        'name' => 'Rhea Sharma',
        'role' => 'Portrait client',
        'quote' => 'It felt effortless and premium from start to finish. The final gallery looked like a story, not just a set of images.',
    ],
    [
        'name' => 'Mitra Studio',
        'role' => 'Brand collaboration',
        'quote' => 'We needed polished content that still felt authentic. Ratnam Digitals delivered exactly that with consistency and attention to detail.',
    ],
];

$packages = [
    [
        'name' => 'Signature Portrait',
        'price' => '₹12,000',
        'description' => 'Perfect for personal branding, lifestyle sessions, or modern portraits.',
        'featured' => false,
        'items' => ['1 hour session', 'Professional editing', '20 final images'],
    ],
    [
        'name' => 'Wedding Story',
        'price' => '₹35,000',
        'description' => 'A complete documentary-style coverage designed around your celebration.',
        'featured' => true,
        'items' => ['8 hours coverage', 'Candid + portrait coverage', 'High-resolution gallery'],
    ],
    [
        'name' => 'Brand Content',
        'price' => '₹22,000',
        'description' => 'Creative, polished imagery for founders, businesses, and campaigns.',
        'featured' => false,
        'items' => ['Creative direction', 'Studio or location shoot', 'Social-ready export set'],
    ],
];
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

<section class="content-section premium-section">
    <div class="section-heading-row">
        <div>
            <span class="section-kicker">Our approach</span>
            <h2 class="section-title left-aligned">A relaxed experience with refined results.</h2>
        </div>
    </div>
    <div class="process-grid">
        <?php foreach ($processSteps as $step): ?>
            <div class="process-card">
                <span class="process-step"><?= htmlspecialchars($step['step']) ?></span>
                <h3><?= htmlspecialchars($step['title']) ?></h3>
                <p><?= htmlspecialchars($step['description']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="content-section testimonial-section">
    <div class="section-heading-row">
        <div>
            <span class="section-kicker">Client love</span>
            <h2 class="section-title left-aligned">The kind of moments people keep coming back to.</h2>
        </div>
    </div>
    <div class="testimonial-grid">
        <?php foreach ($testimonials as $testimonial): ?>
            <article class="testimonial-card">
                <div class="quote-mark">“</div>
                <p><?= htmlspecialchars($testimonial['quote']) ?></p>
                <div class="testimonial-meta">
                    <strong><?= htmlspecialchars($testimonial['name']) ?></strong>
                    <span><?= htmlspecialchars($testimonial['role']) ?></span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="content-section brand-story-section">
    <div class="brand-story-layout">
        <div class="brand-story-image">
            <img src="/static/images/logo.jpg" alt="Ratnam Digitals studio portrait" />
        </div>
        <div class="brand-story-copy">
            <span class="section-kicker">Our Story</span>
            <h2 class="section-title left-aligned">We photograph the real feeling behind every milestone.</h2>
            <p>Ratnam Digitals is built around honest, elegant storytelling. From weddings and portraits to commercial work, we focus on natural emotion, refined composition, and images that feel deeply personal.</p>
            <p>Every session is approached with care, clarity, and an eye for detail so the results feel timeless rather than staged.</p>
            <a href="/about" class="btn btn-primary">Learn more</a>
        </div>
    </div>
</section>

<section class="content-section pricing-section">
    <div class="section-heading-row">
        <div>
            <span class="section-kicker">Packages</span>
            <h2 class="section-title left-aligned">Thoughtful pricing for meaningful moments.</h2>
        </div>
    </div>
    <div class="pricing-grid">
        <?php foreach ($packages as $package): ?>
            <article class="pricing-card<?= !empty($package['featured']) ? ' featured' : '' ?>">
                <?php if (!empty($package['featured'])): ?><span class="package-tag">Most Popular</span><?php endif; ?>
                <h3><?= htmlspecialchars($package['name']) ?></h3>
                <div class="price-wrap">
                    <span class="price"><?= htmlspecialchars($package['price']) ?></span>
                    <span class="price-note">starting</span>
                </div>
                <p><?= htmlspecialchars($package['description']) ?></p>
                <ul>
                    <?php foreach ($package['items'] as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="/book" class="btn btn-primary">Book this</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php if (!empty($services)): ?>
<section class="content-section">
    <h2 class="section-title">Our Services</h2>
    <p class="section-subtitle">Professional photography for every occasion</p>
    <div class="services-grid">
        <?php foreach ($services as $service): ?>
            <div class="service-card<?= !empty($service['background_image']) ? ' service-card-has-photo' : '' ?>">
                <?php if (!empty($service['background_image'])): ?>
                    <img class="service-card-photo" src="/<?= htmlspecialchars($service['background_image']) ?>" alt="<?= htmlspecialchars($service['title']) ?>" style="object-position: <?= number_format((float) ($service['service_crop_x'] ?? 50), 2, '.', '') ?>% <?= number_format((float) ($service['service_crop_y'] ?? 50), 2, '.', '') ?>%;">
                <?php endif; ?>
                <?php if (!empty($service['service_icon'])): ?><span class="feature-icon" aria-hidden="true"><?= htmlspecialchars($service['service_icon']) ?></span><?php endif; ?>
                <h3><?= htmlspecialchars($service['title']) ?></h3>
                <p><?= htmlspecialchars($service['description']) ?></p>
                <div class="service-note">Custom pricing</div>
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

<section class="content-section final-cta-section">
    <div class="final-cta-panel">
        <div>
            <span class="section-kicker">Your story deserves better than a basic album</span>
            <h2>Let’s plan a session that feels as personal as the memory itself.</h2>
        </div>
        <div class="final-cta-actions">
            <a href="/book" class="btn btn-primary">Book your session</a>
            <a href="/contact" class="btn btn-outline">Talk to us</a>
        </div>
    </div>
</section>

<script src="/static/js/hero-carousel.js"></script>
<script src="/static/js/carousel.js"></script>

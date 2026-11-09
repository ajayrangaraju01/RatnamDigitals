<?php
$title = 'Services - Ratnam Digitals';
$services = getActiveServices();
?>

<h1 class="page-title">Our Services</h1>
<p class="section-subtitle">Creative photography experiences tailored to your story</p>

<?php if (!empty($services)): ?>
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
<?php else: ?>
<p class="section-subtitle">No services are available yet.</p>
<?php endif; ?>

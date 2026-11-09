<?php
$title = 'Services - Ratnam Digitals';
$services = getActiveServices();
?>

<h1 class="page-title">Our Services</h1>
<p class="section-subtitle">Creative photography experiences tailored to your story</p>

<?php if (!empty($services)): ?>
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
<?php else: ?>
<p class="section-subtitle">No services are available yet.</p>
<?php endif; ?>

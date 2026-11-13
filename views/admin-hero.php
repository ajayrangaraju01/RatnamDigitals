<h1 class="page-title">Hero Slides</h1>

<form method="post" action="/admin?page=hero" enctype="multipart/form-data" class="admin-form">
    <input type="hidden" name="hero_action" value="add">
    <div class="form-group">
        <label for="title">Title</label>
        <input id="title" name="title" type="text" required>
    </div>
    <div class="form-group">
        <label for="subtitle">Subtitle</label>
        <input id="subtitle" name="subtitle" type="text">
    </div>
    <div class="form-group">
        <label for="media_file">Image or Video</label>
        <input id="media_file" name="media_file" type="file" accept="image/*,video/*" data-image-crop="1.777778">
    </div>
    <div class="form-group">
        <label for="button_text">Button Text</label>
        <input id="button_text" name="button_text" type="text" value="Book a Session">
    </div>
    <div class="form-group">
        <label for="button_link">Button Link</label>
        <input id="button_link" name="button_link" type="text" value="/book">
    </div>
    <div class="form-group">
        <label for="order_index">Order</label>
        <input id="order_index" name="order_index" type="number" value="0">
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
    </div>
    <button type="submit" class="btn btn-primary">Add Hero Slide</button>
</form>

<div style="margin-top: 2rem;">
    <h2>Current Slides</h2>
    <?php foreach ($heroSlides as $slide): ?>
        <article class="admin-card" style="margin-top: 1rem;">
            <?php if (!empty($slide['media_file']) && !preg_match('/\.(mp4|webm|mov)$/i', $slide['media_file'])): ?>
                <img class="admin-hero-preview" src="/<?= htmlspecialchars($slide['media_file']) ?>" alt="<?= htmlspecialchars($slide['title']) ?>">
            <?php endif; ?>
            <h3><?= htmlspecialchars($slide['title']) ?><?= empty($slide['is_active']) ? ' (inactive)' : '' ?></h3>
            <details class="admin-image-edit">
                <summary>Edit slide</summary>
                <form method="post" action="/admin?page=hero" enctype="multipart/form-data" class="admin-form" style="margin-top: 1rem;">
                    <input type="hidden" name="hero_action" value="update">
                    <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                    <div class="form-group">
                        <label for="hero-title-<?= (int) $slide['id'] ?>">Title</label>
                        <input id="hero-title-<?= (int) $slide['id'] ?>" name="title" type="text" value="<?= htmlspecialchars($slide['title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="hero-subtitle-<?= (int) $slide['id'] ?>">Subtitle</label>
                        <input id="hero-subtitle-<?= (int) $slide['id'] ?>" name="subtitle" type="text" value="<?= htmlspecialchars($slide['subtitle'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="hero-media-<?= (int) $slide['id'] ?>">Replace image or video (optional)</label>
                        <input id="hero-media-<?= (int) $slide['id'] ?>" name="media_file" type="file" accept="image/*,video/*" data-image-crop="1.777778">
                    </div>
                    <div class="form-group">
                        <label for="hero-button-text-<?= (int) $slide['id'] ?>">Button Text</label>
                        <input id="hero-button-text-<?= (int) $slide['id'] ?>" name="button_text" type="text" value="<?= htmlspecialchars($slide['button_text'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="hero-button-link-<?= (int) $slide['id'] ?>">Button Link</label>
                        <input id="hero-button-link-<?= (int) $slide['id'] ?>" name="button_link" type="text" value="<?= htmlspecialchars($slide['button_link'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="hero-order-<?= (int) $slide['id'] ?>">Order</label>
                        <input id="hero-order-<?= (int) $slide['id'] ?>" name="order_index" type="number" value="<?= (int) $slide['order_index'] ?>">
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_active" value="1" <?= !empty($slide['is_active']) ? 'checked' : '' ?>> Active</label>
                    </div>
                    <button class="mini-btn secondary" type="submit">Save Changes</button>
                </form>
            </details>
            <form method="post" action="/admin?page=hero" style="margin-top: 0.75rem;">
                <input type="hidden" name="hero_action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                <button class="mini-btn" type="submit">Delete</button>
            </form>
        </article>
            <?php endforeach; ?>
</div>
<script src="/static/js/image-upload-crop.js"></script>

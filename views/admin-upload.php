<h1 class="page-title">Upload Image</h1>

<form method="post" action="/admin?page=upload" enctype="multipart/form-data" class="admin-form">
    <div class="form-group">
        <label for="file">Image File</label>
        <input id="file" name="file" type="file" accept="image/*" data-image-crop="1.333333" required>
    </div>
    <div class="form-group">
        <label for="title">Title</label>
        <input id="title" name="title" type="text">
    </div>
    <div class="form-group">
        <label for="category">Category</label>
        <select id="category" name="category">
            <?php foreach (getImageCategories() as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="caption">Caption</label>
        <textarea id="caption" name="caption"></textarea>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_featured" value="1"> Mark as featured</label>
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
</form>

<div style="margin-top: 2rem;">
    <h2>Image Library</h2>
    <p class="section-subtitle">Edit a photo's details, replace its image, adjust its framing, or prioritize it for homepage features.</p>
    <?php if (!empty($images)): ?>
    <div class="admin-image-grid">
        <?php foreach ($images as $image): ?>
            <article class="admin-image-card">
                <div class="admin-crop-preview">
                    <img src="/<?= htmlspecialchars($image['file']) ?>" alt="<?= htmlspecialchars($image['title'] ?: 'Uploaded photography') ?>" style="object-position: <?= number_format((float) ($image['carousel_crop_x'] ?? 50), 2, '.', '') ?>% <?= number_format((float) ($image['carousel_crop_y'] ?? 50), 2, '.', '') ?>%;">
                </div>
                <div class="admin-image-controls">
                    <h3><?= htmlspecialchars($image['title'] ?: 'Untitled photo') ?></h3>
                    <details class="admin-image-edit">
                        <summary>Edit photo</summary>
                        <form method="post" action="/admin?page=upload" enctype="multipart/form-data" class="admin-crop-form">
                            <input type="hidden" name="image_action" value="update">
                            <input type="hidden" name="id" value="<?= (int) $image['id'] ?>">
                            <div class="form-group">
                                <label for="image-title-<?= (int) $image['id'] ?>">Title</label>
                                <input id="image-title-<?= (int) $image['id'] ?>" name="title" type="text" value="<?= htmlspecialchars($image['title'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="image-category-<?= (int) $image['id'] ?>">Category</label>
                                <select id="image-category-<?= (int) $image['id'] ?>" name="category">
                                    <?php foreach (getImageCategories() as $key => $label): ?>
                                        <option value="<?= htmlspecialchars($key) ?>" <?= $image['category'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="image-caption-<?= (int) $image['id'] ?>">Caption</label>
                                <textarea id="image-caption-<?= (int) $image['id'] ?>" name="caption"><?= htmlspecialchars($image['caption'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="replace-file-<?= (int) $image['id'] ?>">Replace image (optional)</label>
                                <input id="replace-file-<?= (int) $image['id'] ?>" name="file" type="file" accept="image/*" data-image-crop="1.333333">
                            </div>
                            <label class="admin-carousel-toggle">
                                <input type="checkbox" name="is_featured" value="1" <?= !empty($image['is_featured']) ? 'checked' : '' ?>>
                                <span>Prioritize in homepage features</span>
                            </label>
                            <p class="admin-crop-hint">Adjust how this photo is framed wherever it appears.</p>
                            <label for="crop-x-<?= (int) $image['id'] ?>">Horizontal framing</label>
                            <input id="crop-x-<?= (int) $image['id'] ?>" type="range" name="crop_x" min="0" max="100" value="<?= number_format((float) ($image['carousel_crop_x'] ?? 50), 0, '.', '') ?>">
                            <label for="crop-y-<?= (int) $image['id'] ?>">Vertical framing</label>
                            <input id="crop-y-<?= (int) $image['id'] ?>" type="range" name="crop_y" min="0" max="100" value="<?= number_format((float) ($image['carousel_crop_y'] ?? 50), 0, '.', '') ?>">
                            <button type="submit" class="mini-btn secondary">Save Changes</button>
                        </form>
                    </details>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <p>No photos have been uploaded yet.</p>
    <?php endif; ?>
</div>
<script src="/static/js/admin-image-crop.js"></script>
<script src="/static/js/image-upload-crop.js"></script>

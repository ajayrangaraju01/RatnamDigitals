<h1 class="page-title">Services</h1>

<form method="post" action="/admin?page=services" enctype="multipart/form-data" class="admin-form">
    <input type="hidden" name="service_action" value="add">
    <div class="form-group">
        <label for="title">Title</label>
        <input id="title" name="title" type="text" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea>
    </div>
    <div class="form-group">
        <label for="price">Price</label>
        <input id="price" name="price" type="text" required>
    </div>
    <div class="form-group">
        <label for="service_icon">Icon</label>
        <input id="service_icon" name="service_icon" type="text" maxlength="12" placeholder="e.g. 📷 or ♡">
    </div>
    <div class="form-group">
        <label for="background_image">Background Image</label>
        <input id="background_image" name="background_image" type="file" accept="image/*" data-image-crop="1.333333">
    </div>
    <div class="form-group">
        <label for="order_index">Order</label>
        <input id="order_index" name="order_index" type="number" value="0">
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
    </div>
    <button type="submit" class="btn btn-primary">Add Service</button>
</form>

<div style="margin-top: 2rem;">
    <h2>Current Services</h2>
    <?php foreach ($services as $service): ?>
        <form method="post" action="/admin?page=services" enctype="multipart/form-data" class="admin-form" style="margin-top: 1rem;">
            <input type="hidden" name="service_action" value="update">
            <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
            <div class="form-group">
                <label for="title-<?= (int) $service['id'] ?>">Title</label>
                <input id="title-<?= (int) $service['id'] ?>" name="title" type="text" value="<?= htmlspecialchars($service['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description-<?= (int) $service['id'] ?>">Description</label>
                <textarea id="description-<?= (int) $service['id'] ?>" name="description" required><?= htmlspecialchars($service['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="price-<?= (int) $service['id'] ?>">Price</label>
                <input id="price-<?= (int) $service['id'] ?>" name="price" type="text" value="<?= htmlspecialchars($service['price']) ?>" required>
            </div>
            <div class="form-group">
                <label for="service_icon-<?= (int) $service['id'] ?>">Icon</label>
                <input id="service_icon-<?= (int) $service['id'] ?>" name="service_icon" type="text" maxlength="12" value="<?= htmlspecialchars($service['service_icon'] ?? '') ?>" placeholder="e.g. 📷 or ♡">
            </div>
            <div class="form-group">
                <label for="background_image-<?= (int) $service['id'] ?>">Photo<?= !empty($service['background_image']) ? ' (currently set)' : '' ?></label>
                <input id="background_image-<?= (int) $service['id'] ?>" name="background_image" type="file" accept="image/*" data-image-crop="1.333333">
            </div>
            <div class="form-group">
                <label for="order_index-<?= (int) $service['id'] ?>">Order</label>
                <input id="order_index-<?= (int) $service['id'] ?>" name="order_index" type="number" value="<?= (int) $service['order_index'] ?>">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_active" value="1" <?= !empty($service['is_active']) ? 'checked' : '' ?>> Active</label>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
        <form method="post" action="/admin?page=services" style="margin-top: 0.5rem;">
            <input type="hidden" name="service_action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
            <button class="mini-btn" type="submit">Delete</button>
        </form>
    <?php endforeach; ?>
</div>
<script src="/static/js/image-upload-crop.js"></script>

<h1 class="page-title">Packages</h1>
<p class="section-subtitle">Create and edit the packages displayed on the homepage.</p>

<form method="post" action="/admin?page=packages" class="admin-form">
    <input type="hidden" name="package_action" value="add">
    <h2>Add Package</h2>
    <div class="form-group">
        <label for="new-package-name">Name</label>
        <input id="new-package-name" name="name" type="text" required>
    </div>
    <div class="form-group">
        <label for="new-package-price">Price</label>
        <input id="new-package-price" name="price" type="text" placeholder="e.g. ₹12,000" required>
    </div>
    <div class="form-group">
        <label for="new-package-description">Description</label>
        <textarea id="new-package-description" name="description" required></textarea>
    </div>
    <div class="form-group">
        <label for="new-package-items">What's included (one item per line)</label>
        <textarea id="new-package-items" name="items" rows="5"></textarea>
    </div>
    <div class="form-group">
        <label for="new-package-order">Display order</label>
        <input id="new-package-order" name="order_index" type="number" value="0">
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_featured" value="1"> Mark as Most Popular</label>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_active" value="1" checked> Show on homepage</label>
    </div>
    <button type="submit" class="btn btn-primary">Add Package</button>
</form>

<section class="admin-package-list">
    <h2>Existing Packages</h2>
    <?php if (!empty($packages)): ?>
        <?php foreach ($packages as $package): ?>
            <article class="admin-card">
                <h3><?= htmlspecialchars($package['name']) ?><?= empty($package['is_active']) ? ' (hidden)' : '' ?></h3>
                <form method="post" action="/admin?page=packages" class="admin-form admin-package-form">
                    <input type="hidden" name="package_action" value="update">
                    <input type="hidden" name="id" value="<?= (int) $package['id'] ?>">
                    <div class="form-group">
                        <label for="package-name-<?= (int) $package['id'] ?>">Name</label>
                        <input id="package-name-<?= (int) $package['id'] ?>" name="name" type="text" value="<?= htmlspecialchars($package['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="package-price-<?= (int) $package['id'] ?>">Price</label>
                        <input id="package-price-<?= (int) $package['id'] ?>" name="price" type="text" value="<?= htmlspecialchars($package['price']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="package-description-<?= (int) $package['id'] ?>">Description</label>
                        <textarea id="package-description-<?= (int) $package['id'] ?>" name="description" required><?= htmlspecialchars($package['description']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="package-items-<?= (int) $package['id'] ?>">What's included (one item per line)</label>
                        <textarea id="package-items-<?= (int) $package['id'] ?>" name="items" rows="5"><?= htmlspecialchars($package['items']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="package-order-<?= (int) $package['id'] ?>">Display order</label>
                        <input id="package-order-<?= (int) $package['id'] ?>" name="order_index" type="number" value="<?= (int) $package['order_index'] ?>">
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_featured" value="1" <?= !empty($package['is_featured']) ? 'checked' : '' ?>> Mark as Most Popular</label>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_active" value="1" <?= !empty($package['is_active']) ? 'checked' : '' ?>> Show on homepage</label>
                    </div>
                    <button type="submit" class="mini-btn secondary">Save Changes</button>
                </form>
                <form method="post" action="/admin?page=packages" class="admin-package-delete">
                    <input type="hidden" name="package_action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $package['id'] ?>">
                    <button type="submit" class="mini-btn">Delete Package</button>
                </form>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No packages have been added.</p>
    <?php endif; ?>
</section>

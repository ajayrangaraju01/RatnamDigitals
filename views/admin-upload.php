<h1 class="page-title">Upload Image</h1>

<form method="post" action="/admin?page=upload" enctype="multipart/form-data" class="admin-form">
    <div class="form-group">
        <label for="file">Image File</label>
        <input id="file" name="file" type="file" accept="image/*" required>
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
    <div class="portfolio-grid">
        <?php foreach ($images as $image): ?>
            <div class="portfolio-item">
                <img src="/<?= htmlspecialchars($image['file']) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
            </div>
        <?php endforeach; ?>
    </div>
</div>

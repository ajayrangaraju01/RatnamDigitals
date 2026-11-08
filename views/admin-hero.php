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
        <input id="media_file" name="media_file" type="file" accept="image/*,video/*">
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
    <table class="admin-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Active</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($heroSlides as $slide): ?>
                <tr>
                    <td><?= htmlspecialchars($slide['title']) ?></td>
                    <td><?= !empty($slide['is_active']) ? 'Yes' : 'No' ?></td>
                    <td>
                        <form method="post" action="/admin?page=hero">
                            <input type="hidden" name="hero_action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                            <button class="mini-btn" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

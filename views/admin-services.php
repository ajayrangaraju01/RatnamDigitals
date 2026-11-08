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
        <label for="background_image">Background Image</label>
        <input id="background_image" name="background_image" type="file" accept="image/*">
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
    <table class="admin-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Price</th>
                <th>Active</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
                <tr>
                    <td><?= htmlspecialchars($service['title']) ?></td>
                    <td><?= htmlspecialchars($service['price']) ?></td>
                    <td><?= !empty($service['is_active']) ? 'Yes' : 'No' ?></td>
                    <td>
                        <form method="post" action="/admin?page=services">
                            <input type="hidden" name="service_action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                            <button class="mini-btn" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

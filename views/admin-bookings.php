<h1 class="page-title">Bookings</h1>

<div style="margin-bottom: 1rem;">
    <a href="/admin?page=bookings&status=all" class="btn-outline <?= $statusFilter === 'all' ? 'active' : '' ?>">All</a>
    <a href="/admin?page=bookings&status=pending" class="btn-outline <?= $statusFilter === 'pending' ? 'active' : '' ?>">Pending</a>
    <a href="/admin?page=bookings&status=confirmed" class="btn-outline <?= $statusFilter === 'confirmed' ? 'active' : '' ?>">Confirmed</a>
    <a href="/admin?page=bookings&status=cancelled" class="btn-outline <?= $statusFilter === 'cancelled' ? 'active' : '' ?>">Cancelled</a>
    <a href="/admin?page=bookings&status=completed" class="btn-outline <?= $statusFilter === 'completed' ? 'active' : '' ?>">Completed</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Client</th>
            <th>Service</th>
            <th>Date</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?= htmlspecialchars($booking['client_name']) ?></td>
                <td><?= htmlspecialchars($booking['service_type']) ?></td>
                <td><?= htmlspecialchars($booking['requested_date']) ?></td>
                <td><?= htmlspecialchars($booking['phone']) ?></td>
                <td>
                    <span class="status-badge"><?= htmlspecialchars($booking['status']) ?></span>
                </td>
                <td>
                    <form method="post" action="/admin?page=bookings">
                        <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                        <select name="status">
                            <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $booking['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="cancelled" <?= $booking['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="completed" <?= $booking['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <div class="admin-actions">
                            <button class="mini-btn secondary" type="submit">Update</button>
                        </div>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

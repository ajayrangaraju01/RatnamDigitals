<h1 class="page-title">Dashboard</h1>

<div class="admin-grid">
    <div class="admin-card">
        <h3>Total Images</h3>
        <p><?= (int) $stats['total_images'] ?></p>
    </div>
    <div class="admin-card">
        <h3>Total Bookings</h3>
        <p><?= (int) $stats['total_bookings'] ?></p>
    </div>
    <div class="admin-card">
        <h3>Pending</h3>
        <p><?= (int) $stats['pending_bookings'] ?></p>
    </div>
    <div class="admin-card">
        <h3>Confirmed</h3>
        <p><?= (int) $stats['confirmed_bookings'] ?></p>
    </div>
</div>

<div style="margin-top: 2rem;">
    <h2>Recent Uploads</h2>
    <div class="portfolio-grid">
        <?php foreach ($recentImages as $image): ?>
            <div class="portfolio-item">
                <img src="/<?= htmlspecialchars($image['file']) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div style="margin-top: 2rem;">
    <h2>Pending Bookings</h2>
    <?php if (!empty($pendingBookings)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingBookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['client_name']) ?></td>
                        <td><?= htmlspecialchars($booking['service_type']) ?></td>
                        <td><?= htmlspecialchars($booking['requested_date']) ?></td>
                        <td><?= htmlspecialchars($booking['email']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending bookings.</p>
    <?php endif; ?>
</div>

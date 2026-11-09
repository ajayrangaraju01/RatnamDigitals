<?php
$title = 'Book a Session - Ratnam Digitals';
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientName = trim($_POST['client_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $serviceType = trim($_POST['service_type'] ?? '');
    $requestedDate = trim($_POST['requested_date'] ?? '');
    $requestedTime = trim($_POST['requested_time'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($clientName !== '' && $email !== '' && $serviceType !== '' && $requestedDate !== '') {
        $pdo = getDb();
        $stmt = $pdo->prepare(
            "INSERT INTO booking_requests (client_name, email, phone, service_type, requested_date, requested_time, notes) VALUES (:client_name, :email, :phone, :service_type, :requested_date, :requested_time, :notes)"
        );
        $stmt->execute([
            ':client_name' => $clientName,
            ':email' => $email,
            ':phone' => $phone,
            ':service_type' => $serviceType,
            ':requested_date' => $requestedDate,
            ':requested_time' => $requestedTime,
            ':notes' => $notes,
        ]);

        addFlash('success', 'Your booking request has been submitted! We will contact you soon.');
        redirect('/book');
    }

    addFlash('error', 'Please fill in all required booking details.');
}

$serviceChoices = getServiceChoices();
?>

<h1 class="page-title">Book Your Session</h1>
<p class="section-subtitle">Tell us about your vision and we'll get in touch</p>

<div class="booking-layout">
    <form method="post" action="/book" class="form-section booking-form">
        <div class="form-group">
            <label for="client_name">Name</label>
            <input id="client_name" name="client_name" type="text" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input id="phone" name="phone" type="tel">
        </div>
        <div class="form-group">
            <label for="service_type">Service</label>
            <select id="service_type" name="service_type" required>
                <option value="">Select a service</option>
                <?php foreach ($serviceChoices as $key => $label): ?>
                    <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="requested_date">Preferred Date</label>
            <input id="requested_date" name="requested_date" type="date" required>
        </div>
        <div class="form-group">
            <label for="requested_time">Preferred Time</label>
            <input id="requested_time" name="requested_time" type="time">
        </div>
        <div class="form-group">
            <label for="notes">Project Details</label>
            <textarea id="notes" name="notes" placeholder="Tell us more about your photoshoot"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>

    <aside class="booking-sidebar">
        <div class="info-panel">
            <span class="section-kicker">Why book with us</span>
            <h3>Simple, personal, and beautifully planned.</h3>
            <ul>
                <li>Friendly guidance from idea to final gallery</li>
                <li>Editorial experience tailored to your story</li>
                <li>Fast response and clear communication</li>
            </ul>
        </div>

        <div class="info-panel muted-panel">
            <span class="section-kicker">Quick contact</span>
            <p><strong>Call:</strong> <a href="tel:+919876543210">+91 98765 43210</a></p>
            <p><strong>Email:</strong> <a href="mailto:info@ratnamdigitals.com">info@ratnamdigitals.com</a></p>
            <p><strong>Studio:</strong> Hyderabad, Telangana</p>
        </div>
    </aside>
</div>

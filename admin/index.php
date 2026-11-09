<?php
require __DIR__ . '/../includes/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = $uri === '' ? '/' : $uri;
$requested = preg_replace('#^/admin/?#', '', $uri);
$requested = trim($requested, '/');
$action = $_GET['page'] ?? $_GET['action'] ?? ($requested !== '' ? $requested : 'dashboard');

if ($action === 'logout') {
    session_destroy();
    redirect('/admin?page=login');
}

if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (validateAdminLogin($username, $password)) {
            $_SESSION['admin_authenticated'] = true;
            addFlash('success', 'Welcome back!');
            redirect('/admin');
        }

        addFlash('error', 'Invalid username or password.');
    }

    $title = 'Admin Login';
    ob_start();
    include __DIR__ . '/../views/admin-login.php';
    $content = ob_get_clean();
    include __DIR__ . '/../views/admin-layout.php';
    exit;
}

if (!isAdminAuthenticated()) {
    redirect('/admin?page=login');
}

switch ($action) {
    case 'dashboard':
        $pdo = getDb();
        $stats = [
            'total_images' => (int) $pdo->query('SELECT COUNT(*) FROM images')->fetchColumn(),
            'total_bookings' => (int) $pdo->query('SELECT COUNT(*) FROM booking_requests')->fetchColumn(),
            'pending_bookings' => (int) $pdo->query("SELECT COUNT(*) FROM booking_requests WHERE status = 'pending'")->fetchColumn(),
            'confirmed_bookings' => (int) $pdo->query("SELECT COUNT(*) FROM booking_requests WHERE status = 'confirmed'")->fetchColumn(),
        ];
        $recentImages = $pdo->query('SELECT * FROM images ORDER BY uploaded_at DESC LIMIT 12')->fetchAll();
        $pendingBookings = $pdo->query("SELECT * FROM booking_requests WHERE status = 'pending' ORDER BY created_at DESC LIMIT 10")->fetchAll();
        $title = 'Dashboard';
        $adminPage = 'dashboard';
        ob_start();
        include __DIR__ . '/../views/admin-dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/admin-layout.php';
        break;

    case 'upload':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['image_action'] ?? '') === 'set_crop') {
            $pdo = getDb();
            $imageId = (int) ($_POST['id'] ?? 0);
            $cropX = max(0, min(100, (float) ($_POST['crop_x'] ?? 50)));
            $cropY = max(0, min(100, (float) ($_POST['crop_y'] ?? 50)));
            $stmt = $pdo->prepare('UPDATE images SET carousel_crop_x = :crop_x, carousel_crop_y = :crop_y WHERE id = :id');
            $stmt->execute([':crop_x' => $cropX, ':crop_y' => $cropY, ':id' => $imageId]);
            addFlash('success', 'Carousel crop saved.');
            redirect('/admin?page=upload');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['image_action'] ?? '') === 'set_featured') {
            $pdo = getDb();
            $imageId = (int) ($_POST['id'] ?? 0);
            $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;

            if ($isFeatured) {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM images WHERE is_featured = 1 AND id != :id');
                $stmt->execute([':id' => $imageId]);
                if ((int) $stmt->fetchColumn() >= 5) {
                    addFlash('error', 'Choose up to five photos for the homepage carousel.');
                    redirect('/admin?page=upload');
                }
            }

            $stmt = $pdo->prepare('UPDATE images SET is_featured = :is_featured WHERE id = :id');
            $stmt->execute([':is_featured' => $isFeatured, ':id' => $imageId]);
            addFlash('success', $isFeatured ? 'Photo added to the homepage carousel.' : 'Photo removed from the homepage carousel.');
            redirect('/admin?page=upload');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $saved = saveUploadedFile($_FILES['file'], 'media/photos');
            if ($saved) {
                $title = trim($_POST['title'] ?? '');
                $category = trim($_POST['category'] ?? 'other');
                $caption = trim($_POST['caption'] ?? '');
                $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;

                $pdo = getDb();
                $stmt = $pdo->prepare('INSERT INTO images (title, file, category, caption, is_featured) VALUES (:title, :file, :category, :caption, :is_featured)');
                $stmt->execute([
                    ':title' => $title,
                    ':file' => $saved,
                    ':category' => $category,
                    ':caption' => $caption,
                    ':is_featured' => $isFeatured,
                ]);
                addFlash('success', 'Image uploaded successfully.');
                redirect('/admin?page=upload');
            }
            addFlash('error', 'Image upload failed.');
        }

        $pdo = getDb();
        $images = $pdo->query('SELECT * FROM images ORDER BY uploaded_at DESC')->fetchAll();
        $title = 'Upload Image';
        $adminPage = 'upload';
        ob_start();
        include __DIR__ . '/../views/admin-upload.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/admin-layout.php';
        break;

    case 'bookings':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['booking_id'])) {
            $pdo = getDb();
            $bookingId = (int) $_POST['booking_id'];
            $status = $_POST['status'] ?? 'pending';
            $stmt = $pdo->prepare('UPDATE booking_requests SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
            $stmt->execute([':status' => $status, ':id' => $bookingId]);
            addFlash('success', 'Booking updated.');
            redirect('/admin?page=bookings');
        }

        $pdo = getDb();
        $statusFilter = $_GET['status'] ?? 'all';
        if ($statusFilter === 'all') {
            $bookings = $pdo->query('SELECT * FROM booking_requests ORDER BY created_at DESC')->fetchAll();
        } else {
            $stmt = $pdo->prepare('SELECT * FROM booking_requests WHERE status = :status ORDER BY created_at DESC');
            $stmt->execute([':status' => $statusFilter]);
            $bookings = $stmt->fetchAll();
        }
        $title = 'Bookings';
        $adminPage = 'bookings';
        ob_start();
        include __DIR__ . '/../views/admin-bookings.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/admin-layout.php';
        break;

    case 'services':
        $pdo = getDb();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['service_action'])) {
            $serviceAction = $_POST['service_action'];

            if ($serviceAction === 'add') {
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $price = trim($_POST['price'] ?? '');
                $serviceIcon = trim($_POST['service_icon'] ?? '');
                $serviceCropX = max(0, min(100, (float) ($_POST['service_crop_x'] ?? 50)));
                $serviceCropY = max(0, min(100, (float) ($_POST['service_crop_y'] ?? 50)));
                $orderIndex = (int) ($_POST['order_index'] ?? 0);
                $isActive = !empty($_POST['is_active']) ? 1 : 0;
                $background = null;
                if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
                    $background = saveUploadedFile($_FILES['background_image'], 'media/services');
                }
                $stmt = $pdo->prepare('INSERT INTO services (title, description, price, service_icon, background_image, service_crop_x, service_crop_y, order_index, is_active) VALUES (:title, :description, :price, :service_icon, :background_image, :service_crop_x, :service_crop_y, :order_index, :is_active)');
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':price' => $price,
                    ':service_icon' => $serviceIcon,
                    ':background_image' => $background,
                    ':service_crop_x' => $serviceCropX,
                    ':service_crop_y' => $serviceCropY,
                    ':order_index' => $orderIndex,
                    ':is_active' => $isActive,
                ]);
                addFlash('success', 'Service added.');
            }

            if ($serviceAction === 'update') {
                $id = (int) ($_POST['id'] ?? 0);
                $existing = $pdo->prepare('SELECT background_image FROM services WHERE id = :id');
                $existing->execute([':id' => $id]);
                $service = $existing->fetch();
                if ($service) {
                    $background = $service['background_image'];
                    if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
                        $background = saveUploadedFile($_FILES['background_image'], 'media/services') ?? $background;
                    }
                    $serviceCropX = max(0, min(100, (float) ($_POST['service_crop_x'] ?? 50)));
                    $serviceCropY = max(0, min(100, (float) ($_POST['service_crop_y'] ?? 50)));
                    $stmt = $pdo->prepare('UPDATE services SET title = :title, description = :description, price = :price, service_icon = :service_icon, background_image = :background_image, service_crop_x = :service_crop_x, service_crop_y = :service_crop_y, order_index = :order_index, is_active = :is_active, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
                    $stmt->execute([
                        ':title' => trim($_POST['title'] ?? ''),
                        ':description' => trim($_POST['description'] ?? ''),
                        ':price' => trim($_POST['price'] ?? ''),
                        ':service_icon' => trim($_POST['service_icon'] ?? ''),
                        ':background_image' => $background,
                        ':service_crop_x' => $serviceCropX,
                        ':service_crop_y' => $serviceCropY,
                        ':order_index' => (int) ($_POST['order_index'] ?? 0),
                        ':is_active' => !empty($_POST['is_active']) ? 1 : 0,
                        ':id' => $id,
                    ]);
                    addFlash('success', 'Service updated.');
                }
            }

            if ($serviceAction === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                $pdo->prepare('DELETE FROM services WHERE id = :id')->execute([':id' => $id]);
                addFlash('success', 'Service deleted.');
            }
        }

        $services = $pdo->query('SELECT * FROM services ORDER BY order_index ASC, id DESC')->fetchAll();
        $title = 'Services';
        $adminPage = 'services';
        ob_start();
        include __DIR__ . '/../views/admin-services.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/admin-layout.php';
        break;

    case 'hero':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['hero_action'])) {
            $pdo = getDb();
            $heroAction = $_POST['hero_action'];

            if ($heroAction === 'add') {
                $title = trim($_POST['title'] ?? '');
                $subtitle = trim($_POST['subtitle'] ?? '');
                $buttonText = trim($_POST['button_text'] ?? 'Book a Session');
                $buttonLink = trim($_POST['button_link'] ?? '/book');
                $orderIndex = (int) ($_POST['order_index'] ?? 0);
                $isActive = !empty($_POST['is_active']) ? 1 : 0;
                $media = null;
                if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
                    $media = saveUploadedFile($_FILES['media_file'], 'media/hero');
                }

                $stmt = $pdo->prepare('INSERT INTO hero_slides (title, subtitle, media_file, button_text, button_link, order_index, is_active) VALUES (:title, :subtitle, :media_file, :button_text, :button_link, :order_index, :is_active)');
                $stmt->execute([
                    ':title' => $title,
                    ':subtitle' => $subtitle,
                    ':media_file' => $media,
                    ':button_text' => $buttonText,
                    ':button_link' => $buttonLink,
                    ':order_index' => $orderIndex,
                    ':is_active' => $isActive,
                ]);
                addFlash('success', 'Hero slide added.');
            }

            if ($heroAction === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                $pdo->prepare('DELETE FROM hero_slides WHERE id = :id')->execute([':id' => $id]);
                addFlash('success', 'Hero slide deleted.');
            }
        }

        $heroSlides = getHeroSlides();
        $title = 'Hero Slides';
        $adminPage = 'hero';
        ob_start();
        include __DIR__ . '/../views/admin-hero.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/admin-layout.php';
        break;

    default:
        redirect('/admin?page=dashboard');
}

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['image_action'] ?? '') === 'update') {
            $pdo = getDb();
            $imageId = (int) ($_POST['id'] ?? 0);
            $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;
            $existingStmt = $pdo->prepare('SELECT file FROM images WHERE id = :id');
            $existingStmt->execute([':id' => $imageId]);
            $existing = $existingStmt->fetch();

            if (!$existing) {
                addFlash('error', 'The selected image could not be found.');
                redirect('/admin?page=upload');
            }

            if ($isFeatured) {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM images WHERE is_featured = 1 AND id != :id');
                $stmt->execute([':id' => $imageId]);
                if ((int) $stmt->fetchColumn() >= 5) {
                    addFlash('error', 'Choose up to five featured photos.');
                    redirect('/admin?page=upload');
                }
            }

            $category = trim($_POST['category'] ?? 'other');
            if (!array_key_exists($category, getImageCategories())) {
                addFlash('error', 'Select a valid image category.');
                redirect('/admin?page=upload');
            }

            $imageFile = $existing['file'];
            if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                    addFlash('error', 'The replacement image could not be uploaded.');
                    redirect('/admin?page=upload');
                }
                $replacement = saveUploadedFile($_FILES['file'], 'media/photos');
                if ($replacement === null) {
                    addFlash('error', 'The replacement image could not be saved.');
                    redirect('/admin?page=upload');
                }
                $imageFile = $replacement;
            }

            $cropX = max(0, min(100, (float) ($_POST['crop_x'] ?? 50)));
            $cropY = max(0, min(100, (float) ($_POST['crop_y'] ?? 50)));
            $stmt = $pdo->prepare('UPDATE images SET title = :title, file = :file, category = :category, caption = :caption, is_featured = :is_featured, carousel_crop_x = :crop_x, carousel_crop_y = :crop_y WHERE id = :id');
            $stmt->execute([
                ':title' => trim($_POST['title'] ?? ''),
                ':file' => $imageFile,
                ':category' => $category,
                ':caption' => trim($_POST['caption'] ?? ''),
                ':is_featured' => $isFeatured,
                ':crop_x' => $cropX,
                ':crop_y' => $cropY,
                ':id' => $imageId,
            ]);
            addFlash('success', 'Image details saved.');
            redirect('/admin?page=upload');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            if (!empty($_POST['is_featured'])) {
                $featuredCount = (int) getDb()->query('SELECT COUNT(*) FROM images WHERE is_featured = 1')->fetchColumn();
                if ($featuredCount >= 5) {
                    addFlash('error', 'Choose up to five featured photos.');
                    redirect('/admin?page=upload');
                }
            }

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

    case 'packages':
        $pdo = getDb();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['package_action'])) {
            $packageAction = $_POST['package_action'];

            if ($packageAction === 'add' || $packageAction === 'update') {
                $name = trim($_POST['name'] ?? '');
                $price = trim($_POST['price'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $items = implode("\n", array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $_POST['items'] ?? '') ?: []), static fn(string $item): bool => $item !== ''));

                if ($name === '' || $price === '' || $description === '') {
                    addFlash('error', 'Enter a package name, price, and description.');
                    redirect('/admin?page=packages');
                }

                if ($packageAction === 'add') {
                    $stmt = $pdo->prepare('INSERT INTO packages (name, price, description, items, is_featured, is_active, order_index) VALUES (:name, :price, :description, :items, :is_featured, :is_active, :order_index)');
                    $stmt->execute([
                        ':name' => $name,
                        ':price' => $price,
                        ':description' => $description,
                        ':items' => $items,
                        ':is_featured' => !empty($_POST['is_featured']) ? 1 : 0,
                        ':is_active' => !empty($_POST['is_active']) ? 1 : 0,
                        ':order_index' => (int) ($_POST['order_index'] ?? 0),
                    ]);
                    addFlash('success', 'Package added.');
                } else {
                    $id = (int) ($_POST['id'] ?? 0);
                    $stmt = $pdo->prepare('UPDATE packages SET name = :name, price = :price, description = :description, items = :items, is_featured = :is_featured, is_active = :is_active, order_index = :order_index, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
                    $stmt->execute([
                        ':name' => $name,
                        ':price' => $price,
                        ':description' => $description,
                        ':items' => $items,
                        ':is_featured' => !empty($_POST['is_featured']) ? 1 : 0,
                        ':is_active' => !empty($_POST['is_active']) ? 1 : 0,
                        ':order_index' => (int) ($_POST['order_index'] ?? 0),
                        ':id' => $id,
                    ]);
                    addFlash('success', 'Package saved.');
                }
            }

            if ($packageAction === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                $stmt = $pdo->prepare('DELETE FROM packages WHERE id = :id');
                $stmt->execute([':id' => $id]);
                $deleted = $stmt->rowCount() > 0;
                addFlash($deleted ? 'success' : 'error', $deleted ? 'Package deleted.' : 'Package not found.');
            }

            redirect('/admin?page=packages');
        }

        $packages = $pdo->query('SELECT * FROM packages ORDER BY order_index ASC, id ASC')->fetchAll();
        $title = 'Packages';
        $adminPage = 'packages';
        ob_start();
        include __DIR__ . '/../views/admin-packages.php';
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

            if ($heroAction === 'update') {
                $id = (int) ($_POST['id'] ?? 0);
                $existingStmt = $pdo->prepare('SELECT media_file FROM hero_slides WHERE id = :id');
                $existingStmt->execute([':id' => $id]);
                $existing = $existingStmt->fetch();

                if (!$existing) {
                    addFlash('error', 'The selected hero slide could not be found.');
                    redirect('/admin?page=hero');
                }

                $media = $existing['media_file'];
                if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    if ($_FILES['media_file']['error'] !== UPLOAD_ERR_OK) {
                        addFlash('error', 'The replacement hero media could not be uploaded.');
                        redirect('/admin?page=hero');
                    }
                    $replacement = saveUploadedFile($_FILES['media_file'], 'media/hero');
                    if ($replacement === null) {
                        addFlash('error', 'The replacement hero media could not be saved.');
                        redirect('/admin?page=hero');
                    }
                    $media = $replacement;
                }

                $stmt = $pdo->prepare('UPDATE hero_slides SET title = :title, subtitle = :subtitle, media_file = :media_file, button_text = :button_text, button_link = :button_link, order_index = :order_index, is_active = :is_active WHERE id = :id');
                $stmt->execute([
                    ':title' => trim($_POST['title'] ?? ''),
                    ':subtitle' => trim($_POST['subtitle'] ?? ''),
                    ':media_file' => $media,
                    ':button_text' => trim($_POST['button_text'] ?? ''),
                    ':button_link' => trim($_POST['button_link'] ?? ''),
                    ':order_index' => (int) ($_POST['order_index'] ?? 0),
                    ':is_active' => !empty($_POST['is_active']) ? 1 : 0,
                    ':id' => $id,
                ]);
                addFlash('success', 'Hero slide updated.');
            }

            if ($heroAction === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                $pdo->prepare('DELETE FROM hero_slides WHERE id = :id')->execute([':id' => $id]);
                addFlash('success', 'Hero slide deleted.');
            }
        }

        $heroSlides = getDb()->query('SELECT * FROM hero_slides ORDER BY order_index ASC, id DESC')->fetchAll();
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

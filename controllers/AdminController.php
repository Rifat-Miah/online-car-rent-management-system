<?php
require_once __DIR__ . '/../models/AdminDashboardModel.php';
require_once __DIR__ . '/../models/AdminCarModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AdminController
{
    private $dashboardModel;
    private $carModel;

    public function __construct()
    {
        $this->dashboardModel = new AdminDashboardModel();
        $this->carModel = new AdminCarModel();
    }

    private function requireAdmin()
    {
        $role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? null;

        if ($role !== 'admin') {
            echo "Access denied. Please login as admin to view this page.";
            exit();
        }
    }

    private function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    private function isValidCsrfToken()
    {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        return !empty($token) && hash_equals($sessionToken, $token);
    }

    private function validateCarInput($data)
    {
        $errors = [];

        $allowedTypes = ['Private car', 'Microbus', 'PickUp', 'SUV', 'Van', 'Sedan'];
        $allowedStatuses = ['available', 'unavailable'];

        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Car name is required.';
        }

        if (empty(trim($data['model'] ?? ''))) {
            $errors['model'] = 'Car model is required.';
        }

        if (empty($data['type']) || !in_array($data['type'], $allowedTypes)) {
            $errors['type'] = 'Please select a valid car type.';
        }

        if (empty($data['price_per_day']) || !is_numeric($data['price_per_day']) || $data['price_per_day'] <= 0) {
            $errors['price_per_day'] = 'Price per day must be greater than 0.';
        }

        if (empty($data['availability_status']) || !in_array($data['availability_status'], $allowedStatuses)) {
            $errors['availability_status'] = 'Please select a valid availability status.';
        }

        return $errors;
    }

    private function uploadCarImage(&$errors)
    {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors['image'] = 'Image upload failed. Please try again.';
            return null;
        }

        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors['image'] = 'Image size must be 2MB or less.';
            return null;
        }

        $imageInfo = getimagesize($_FILES['image']['tmp_name']);

        if ($imageInfo === false) {
            $errors['image'] = 'Uploaded file is not a valid image.';
            return null;
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png'
        ];

        $mimeType = $imageInfo['mime'] ?? '';

        if (!array_key_exists($mimeType, $allowedMimeTypes)) {
            $errors['image'] = 'Only JPG and PNG images are allowed.';
            return null;
        }

        $uploadDir = __DIR__ . '/../public/uploads/cars/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = $allowedMimeTypes[$mimeType];
        $fileName = 'car_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $errors['image'] = 'Could not save uploaded image.';
            return null;
        }

        return 'public/uploads/cars/' . $fileName;
    }

    public function dashboard()
    {
        $this->requireAdmin();

        $totalCars = $this->dashboardModel->getTotalCars();
        $totalMembers = $this->dashboardModel->getTotalMembers();
        $totalOrders = $this->dashboardModel->getTotalOrders();
        $totalBlogs = $this->dashboardModel->getTotalBlogs();

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function cars()
    {
        $this->requireAdmin();

        $cars = $this->carModel->getAllCars();
$csrfToken = $this->generateCsrfToken();

require_once __DIR__ . '/../views/admin/cars.php';
    }

    public function createCar()
    {
        $this->requireAdmin();

        $errors = [];
        $car = [
            'name' => '',
            'model' => '',
            'type' => '',
            'price_per_day' => '',
            'availability_status' => 'available',
            'description' => ''
        ];

        $formTitle = 'Add New Car';
        $formAction = 'index.php?controller=adminCars&action=storeCar';
        $csrfToken = $this->generateCsrfToken();

        require_once __DIR__ . '/../views/admin/car_form.php';
    }

    public function storeCar()
    {
        $this->requireAdmin();

        $errors = [];

        if (!$this->isValidCsrfToken()) {
            $errors['general'] = 'Invalid form request. Please try again.';
        }

        $car = [
            'name' => trim($_POST['name'] ?? ''),
            'model' => trim($_POST['model'] ?? ''),
            'type' => $_POST['type'] ?? '',
            'price_per_day' => $_POST['price_per_day'] ?? '',
            'availability_status' => $_POST['availability_status'] ?? 'available',
            'description' => trim($_POST['description'] ?? '')
        ];

        $errors = array_merge($errors, $this->validateCarInput($car));

        $imagePath = $this->uploadCarImage($errors);

        if (!empty($errors)) {
            $formTitle = 'Add New Car';
            $formAction = 'index.php?controller=adminCars&action=storeCar';
            $csrfToken = $this->generateCsrfToken();

            require_once __DIR__ . '/../views/admin/car_form.php';
            return;
        }

        $car['image_path'] = $imagePath;

        $this->carModel->createCar($car);

        header("Location: index.php?controller=adminCars&success=created");
        exit();
    }

    public function editCar()
{
    $this->requireAdmin();

    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        header("Location: index.php?controller=adminCars&error=invalid");
        exit();
    }

    $car = $this->carModel->getCarById($id);

    if (!$car) {
        header("Location: index.php?controller=adminCars&error=notfound");
        exit();
    }

    $errors = [];
    $formTitle = 'Edit Car';
    $formAction = 'index.php?controller=adminCars&action=updateCar&id=' . urlencode($id);
    $csrfToken = $this->generateCsrfToken();

    require_once __DIR__ . '/../views/admin/car_form.php';
}

public function updateCar()
{
    $this->requireAdmin();

    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        header("Location: index.php?controller=adminCars&error=invalid");
        exit();
    }

    $existingCar = $this->carModel->getCarById($id);

    if (!$existingCar) {
        header("Location: index.php?controller=adminCars&error=notfound");
        exit();
    }

    $errors = [];

    if (!$this->isValidCsrfToken()) {
        $errors['general'] = 'Invalid form request. Please try again.';
    }

    $car = [
        'id' => $id,
        'name' => trim($_POST['name'] ?? ''),
        'model' => trim($_POST['model'] ?? ''),
        'type' => $_POST['type'] ?? '',
        'price_per_day' => $_POST['price_per_day'] ?? '',
        'availability_status' => $_POST['availability_status'] ?? 'available',
        'description' => trim($_POST['description'] ?? ''),
        'image_path' => $existingCar['image_path'] ?? null
    ];

    $errors = array_merge($errors, $this->validateCarInput($car));

    $newImagePath = $this->uploadCarImage($errors);

    if ($newImagePath !== null) {
        if (!empty($existingCar['image_path'])) {
            $oldImageFullPath = __DIR__ . '/../' . $existingCar['image_path'];

            if (file_exists($oldImageFullPath)) {
                unlink($oldImageFullPath);
            }
        }

        $car['image_path'] = $newImagePath;
    }

    if (!empty($errors)) {
        $formTitle = 'Edit Car';
        $formAction = 'index.php?controller=adminCars&action=updateCar&id=' . urlencode($id);
        $csrfToken = $this->generateCsrfToken();

        require_once __DIR__ . '/../views/admin/car_form.php';
        return;
    }

    $this->carModel->updateCar($id, $car);

    header("Location: index.php?controller=adminCars&success=updated");
    exit();
}
    
   public function deleteCar()
{
    $this->requireAdmin();

    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        header("Location: index.php?controller=adminCars&error=invalid");
        exit();
    }

    if (!$this->isValidCsrfToken()) {
        header("Location: index.php?controller=adminCars&error=csrf");
        exit();
    }

    $car = $this->carModel->getCarById($id);

    if (!$car) {
        header("Location: index.php?controller=adminCars&error=notfound");
        exit();
    }

    if ($this->carModel->carHasActiveOrders($id)) {
        header("Location: index.php?controller=adminCars&error=active_orders");
        exit();
    }

    $deleted = $this->carModel->deleteCar($id);

    if ($deleted && !empty($car['image_path'])) {
        $imageFullPath = __DIR__ . '/../' . $car['image_path'];

        if (file_exists($imageFullPath)) {
            unlink($imageFullPath);
        }
    }

    header("Location: index.php?controller=adminCars&success=deleted");
    exit();
}
}

$route = $_GET['controller'] ?? 'adminDashboard';
$action = $_GET['action'] ?? null;

if ($action === null) {
    switch ($route) {
        case 'adminCars':
            $action = 'cars';
            break;

        case 'adminDashboard':
        default:
            $action = 'dashboard';
            break;
    }
}

$controller = new AdminController();

switch ($action) {
    case 'cars':
        $controller->cars();
        break;

    case 'createCar':
        $controller->createCar();
        break;

    case 'storeCar':
        $controller->storeCar();
        break;

    case 'editCar':
        $controller->editCar();
        break;

    case 'updateCar':
        $controller->updateCar();
        break;

    case 'deleteCar':
    $controller->deleteCar();
    break;    

    case 'dashboard':
    default:
        $controller->dashboard();
        break;
}
?>
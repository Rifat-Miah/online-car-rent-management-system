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

        require_once __DIR__ . '/../views/admin/cars.php';
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

    case 'dashboard':
    default:
        $controller->dashboard();
        break;
}
?>
<?php
require_once __DIR__ . '/../models/AdminDashboardModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AdminController
{
    private $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new AdminDashboardModel();
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
}

$action = $_GET['action'] ?? 'dashboard';

$controller = new AdminController();

switch ($action) {
    case 'dashboard':
    default:
        $controller->dashboard();
        break;
}
?>
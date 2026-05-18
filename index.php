<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = $_GET['controller'] ?? 'blog';

switch ($controller) {
    case 'adminDashboard':
    case 'adminCars':
    case 'adminMembers':
    case 'adminOrders':
        require_once __DIR__ . '/controllers/AdminController.php';
        break;

    case 'blog':
    default:
        require_once __DIR__ . '/controllers/BlogController.php';
        break;
}
?>
<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
    $_SESSION['user_name'] = 'Test Member';
    $_SESSION['user_role'] = 'member';
}

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'cars';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

if ($controller === 'cars') {
    require_once 'controllers/carController.php';
} elseif ($controller === 'order') {
    require_once 'controllers/orderController.php';
} elseif ($controller === 'invoice') {
    require_once 'controllers/invoiceController.php';
} elseif ($controller === 'history') {
    require_once 'controllers/historyController.php';
} else {
    echo "404 Not Found";
}
?>
<?php
if ($_SESSION['user_role'] !== 'member') die("Members only.");

require_once 'models/carModel.php';
require_once 'models/orderModel.php';

if ($action === 'calculateCost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $car = getCarById($_POST['car_id']);
    
    $start = strtotime($_POST['start_date']);
    $end = strtotime($_POST['end_date']);
    $today = strtotime(date('Y-m-d'));
    
    if ($start < $today || $end <= $start) {
        echo json_encode(['error' => 'Invalid dates selected.']);
        exit();
    }
    
    $days = ceil(($end - $start) / 86400);
    $total = $days * $car['price_per_day'];
    
    echo json_encode(['total' => $total, 'days' => $days]);
    exit();
}

if ($action === 'placeOrder' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = $_POST['car_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    $car = getCarById($car_id);
    $days = ceil((strtotime($end_date) - strtotime($start_date)) / 86400);
    $total_cost = $days * $car['price_per_day'];
    
    $order_id = createOrder($_SESSION['user_id'], $car_id, $start_date, $end_date, $total_cost);
    
    if ($order_id) {
        header("Location: index.php?controller=invoice&id=" . $order_id);
    }
    exit();
}

if ($action === 'view') {
    $car = getCarById($_GET['car_id']);
    require_once 'views/carDetails.php';
}
?><?php

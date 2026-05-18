<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'member') {
    header("Location: index.php?controller=login");
    exit();
}

require_once 'models/orderModel.php';


cancelAbandonedOrders((int)$_SESSION['user_id']);

$user_id = (int)$_SESSION['user_id'];
$orders = getRentalHistoryByUserId($user_id);

require_once 'views/history.php';
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'models/carModel.php';
require_once 'models/orderModel.php';


if (isset($_SESSION['user_id'])) {
    cancelAbandonedOrders((int)$_SESSION['user_id']);
}

$cars = getAllAvailableCars();
require_once 'views/carList.php';
?>
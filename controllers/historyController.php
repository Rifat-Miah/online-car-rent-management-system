<?php
if ($_SESSION['user_role'] !== 'member') die("Members only.");
require_once 'models/orderModel.php';

$orders = getOrdersByUser($_SESSION['user_id']);
require_once 'views/history.php';
?>
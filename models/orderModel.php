<?php
require_once 'models/database.php';

function createOrder($user_id, $car_id, $start_date, $end_date, $total_cost) {
    global $con;
    
    $stmt = mysqli_prepare($con, "INSERT INTO orders (user_id, car_id, start_date, end_date, total_cost, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    mysqli_stmt_bind_param($stmt, "iissd", $user_id, $car_id, $start_date, $end_date, $total_cost);
    
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($con);
    }
    return false;
}

function getOrderById($order_id) {
    global $con;
    
    $stmt = mysqli_prepare($con, "SELECT o.*, c.name as car_name, c.model FROM orders o JOIN cars c ON o.car_id = c.id WHERE o.id = ?");
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function updateOrderStatus($order_id, $status, $payment_method = null) {
    global $con;
    
    if ($payment_method) {
        $stmt = mysqli_prepare($con, "UPDATE orders SET status = ?, payment_method = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $status, $payment_method, $order_id);
    } else {
        $stmt = mysqli_prepare($con, "UPDATE orders SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    }
    return mysqli_stmt_execute($stmt);
}

function getOrdersByUser($user_id) {
    global $con;
    
    $stmt = mysqli_prepare($con, "SELECT o.*, c.name as car_name, c.model FROM orders o JOIN cars c ON o.car_id = c.id WHERE o.user_id = ? ORDER BY o.order_date DESC");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>
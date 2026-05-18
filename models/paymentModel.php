<?php
require_once 'models/database.php';

function createPayment($order_id, $amount, $method, $transaction_id) {
    global $con;
    
    $stmt = mysqli_prepare($con, "INSERT INTO payments (order_id, amount, payment_method, transaction_id) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "idss", $order_id, $amount, $method, $transaction_id);
    
    return mysqli_stmt_execute($stmt);
}
?>
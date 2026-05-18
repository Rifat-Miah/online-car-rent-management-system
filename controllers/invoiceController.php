<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'member') {
    header("Location: index.php?controller=login");
    exit();
}

require_once 'models/orderModel.php';
require_once 'models/paymentModel.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = getOrderById($order_id);

if (!$order || $order['user_id'] !== $_SESSION['user_id']) {
    die("Access denied.");
}

$action = isset($_GET['action']) ? $_GET['action'] : 'view';

if ($action === 'cancel' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    if (ob_get_length()) ob_clean(); 
    
    header('Content-Type: application/json');
    
    $success = deleteOrder($order_id);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database failed to delete the record.']);
    }
    exit(); 
}



if ($action === 'pay' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['payment_method'];
    $txn_id = "TXN" . time() . rand(100,999); 
    
    createPayment($order_id, $order['total_cost'], $method, $txn_id);
    updateOrderStatus($order_id, 'confirmed', $method);
    
    header("Location: index.php?controller=history&msg=success");
    exit();
}

require_once 'views/invoice.php';
?>
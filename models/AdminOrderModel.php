<?php
require_once __DIR__ . '/database.php';

class AdminOrderModel
{
    private $conn;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getAllOrders()
    {
        $sql = "SELECT 
                    orders.id,
                    orders.start_date,
                    orders.end_date,
                    orders.total_cost,
                    orders.status,
                    orders.payment_method,
                    orders.order_date,
                    users.name AS member_name,
                    users.email AS member_email,
                    cars.name AS car_name,
                    cars.model AS car_model,
                    cars.type AS car_type
                FROM orders
                LEFT JOIN users ON orders.user_id = users.id
                LEFT JOIN cars ON orders.car_id = cars.id
                ORDER BY orders.order_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
?>
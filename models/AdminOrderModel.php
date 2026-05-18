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

    public function getOrders($filters = [])
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
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND orders.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['from_date'])) {
            $sql .= " AND DATE(orders.order_date) >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= " AND DATE(orders.order_date) <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }

        $sql .= " ORDER BY orders.order_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
?>
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

        $types = "";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND orders.status = ?";
            $types .= "s";
            $params[] = $filters['status'];
        }

        if (!empty($filters['from_date'])) {
            $sql .= " AND DATE(orders.order_date) >= ?";
            $types .= "s";
            $params[] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= " AND DATE(orders.order_date) <= ?";
            $types .= "s";
            $params[] = $filters['to_date'];
        }

        $sql .= " ORDER BY orders.order_date DESC";

        $stmt = $this->conn->prepare($sql);

        if (!empty($params)) {
            $this->bindParams($stmt, $types, $params);
        }

        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function bindParams($stmt, $types, $params)
    {
        $bindValues = [];
        $bindValues[] = $types;

        foreach ($params as $key => $value) {
            $bindValues[] = &$params[$key];
        }

        call_user_func_array([$stmt, 'bind_param'], $bindValues);
    }
}
?>
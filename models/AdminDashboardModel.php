<?php
require_once __DIR__ . '/database.php';

class AdminDashboardModel
{
    private $conn;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getTotalCars()
    {
        $sql = "SELECT COUNT(*) AS total FROM cars";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    public function getTotalMembers()
    {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'member'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    public function getTotalOrders()
    {
        $sql = "SELECT COUNT(*) AS total FROM orders";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    public function getTotalBlogs()
    {
        $sql = "SELECT COUNT(*) AS total FROM blogs";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
}
?>
<?php
require_once __DIR__ . '/database.php';

class AdminCarModel
{
    private $conn;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getAllCars()
    {
        $sql = "SELECT * FROM cars ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
?>
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

    public function createCar($data)
    {
        $sql = "INSERT INTO cars 
                (name, model, type, price_per_day, availability_status, image_path, description) 
                VALUES 
                (:name, :model, :type, :price_per_day, :availability_status, :image_path, :description)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $data['name'],
            ':model' => $data['model'],
            ':type' => $data['type'],
            ':price_per_day' => $data['price_per_day'],
            ':availability_status' => $data['availability_status'],
            ':image_path' => $data['image_path'],
            ':description' => $data['description']
        ]);
    }
}
?>
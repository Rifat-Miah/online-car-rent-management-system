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

    public function getCarById($id)
    {
        $sql = "SELECT * FROM cars WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
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

    public function updateCar($id, $data)
    {
        $sql = "UPDATE cars 
                SET 
                    name = :name,
                    model = :model,
                    type = :type,
                    price_per_day = :price_per_day,
                    availability_status = :availability_status,
                    image_path = :image_path,
                    description = :description
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $data['name'],
            ':model' => $data['model'],
            ':type' => $data['type'],
            ':price_per_day' => $data['price_per_day'],
            ':availability_status' => $data['availability_status'],
            ':image_path' => $data['image_path'],
            ':description' => $data['description'],
            ':id' => $id
        ]);
    }

    public function carHasActiveOrders($id)
{
    $sql = "SELECT COUNT(*) AS total 
            FROM orders 
            WHERE car_id = :id 
            AND status IN ('pending', 'confirmed')";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':id' => $id
    ]);

    $result = $stmt->fetch();

    return $result['total'] > 0;
}

public function deleteCar($id)
{
    $sql = "DELETE FROM cars WHERE id = :id";
    $stmt = $this->conn->prepare($sql);

    return $stmt->execute([
        ':id' => $id
    ]);
}
}
?>
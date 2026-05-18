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

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCarById($id)
    {
        $sql = "SELECT * FROM cars WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        $id = (int) $id;
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function createCar($data)
    {
        $sql = "INSERT INTO cars 
                (name, model, type, price_per_day, availability_status, image_path, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $name = $data['name'];
        $model = $data['model'];
        $type = $data['type'];
        $pricePerDay = $data['price_per_day'];
        $availabilityStatus = $data['availability_status'];
        $imagePath = $data['image_path'];
        $description = $data['description'];

        $stmt->bind_param(
            "sssdsss",
            $name,
            $model,
            $type,
            $pricePerDay,
            $availabilityStatus,
            $imagePath,
            $description
        );

        return $stmt->execute();
    }

    public function updateCar($id, $data)
    {
        $sql = "UPDATE cars 
                SET 
                    name = ?,
                    model = ?,
                    type = ?,
                    price_per_day = ?,
                    availability_status = ?,
                    image_path = ?,
                    description = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $name = $data['name'];
        $model = $data['model'];
        $type = $data['type'];
        $pricePerDay = $data['price_per_day'];
        $availabilityStatus = $data['availability_status'];
        $imagePath = $data['image_path'];
        $description = $data['description'];
        $id = (int) $id;

        $stmt->bind_param(
            "sssdsssi",
            $name,
            $model,
            $type,
            $pricePerDay,
            $availabilityStatus,
            $imagePath,
            $description,
            $id
        );

        return $stmt->execute();
    }

    public function carHasActiveOrders($id)
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM orders 
                WHERE car_id = ? 
                AND status IN ('pending', 'confirmed')";

        $stmt = $this->conn->prepare($sql);

        $id = (int) $id;
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        return ($result['total'] ?? 0) > 0;
    }

    public function deleteCar($id)
    {
        $sql = "DELETE FROM cars WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        $id = (int) $id;
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}
?>
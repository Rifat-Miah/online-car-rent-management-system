<?php
require_once __DIR__ . '/database.php';

class AdminMemberModel
{
    private $conn;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getAllMembers()
    {
        $sql = "SELECT id, name, email, phone, address, created_at 
                FROM users 
                WHERE role = 'member' 
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMemberById($id)
    {
        $sql = "SELECT id, name, email, role 
                FROM users 
                WHERE id = ? AND role = 'member'";

        $stmt = $this->conn->prepare($sql);

        $id = (int) $id;
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function deleteMember($id)
    {
        $sql = "DELETE FROM users 
                WHERE id = ? AND role = 'member'";

        $stmt = $this->conn->prepare($sql);

        $id = (int) $id;
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}
?>
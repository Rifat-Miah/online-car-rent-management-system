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

        return $stmt->fetchAll();
    }

    public function getMemberById($id)
    {
        $sql = "SELECT id, name, email, role 
                FROM users 
                WHERE id = :id AND role = 'member'";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }

    public function deleteMember($id)
    {
        $sql = "DELETE FROM users 
                WHERE id = :id AND role = 'member'";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}
?>
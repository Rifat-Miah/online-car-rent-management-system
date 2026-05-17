<?php
require_once __DIR__ . '/database.php';

class Blog {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllPosts($limit = null, $offset = null, $search = '') {
        try {
            $sql = "SELECT b.*, u.name as author_name, u.role as author_role
                    FROM blogs b 
                    LEFT JOIN users u ON b.user_id = u.id";
            
            // Add search condition if search term is provided - CASE INSENSITIVE
            if (!empty($search)) {
                $sql .= " WHERE (LOWER(b.title) LIKE LOWER(?) OR LOWER(b.content) LIKE LOWER(?))";
            }
            
            $sql .= " ORDER BY b.created_at DESC";
            
            if ($limit !== null && $offset !== null) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            } elseif ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit;
            }
            
            $stmt = $this->db->prepare($sql);
            
            if (!empty($search)) {
                $searchParam = '%' . $search . '%';
                $stmt->execute([$searchParam, $searchParam]);
            } else {
                $stmt->execute();
            }
            
            $result = $stmt->fetchAll();
            
            // Calculate dynamic read counts and image paths
            foreach ($result as &$post) {
                $post['read_count'] = $this->calculateReadCount($post['created_at'], $post['id']);
                $post['image_path'] = $this->getPostImage($post['id']);
            }
            
            return $result ?: [];
        } catch (PDOException $e) {
            error_log("Database error in getAllPosts: " . $e->getMessage());
            return [];
        }
    }
    
    public function getTotalCount($search = '') {
        try {
            $sql = "SELECT COUNT(*) as total FROM blogs";
            
            if (!empty($search)) {
                $sql .= " WHERE (LOWER(title) LIKE LOWER(?) OR LOWER(content) LIKE LOWER(?))";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if (!empty($search)) {
                $searchParam = '%' . $search . '%';
                $stmt->execute([$searchParam, $searchParam]);
            } else {
                $stmt->execute();
            }
            
            $result = $stmt->fetch();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Database error in getTotalCount: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getPostById($id) {
        try {
            $sql = "SELECT b.*, u.name as author_name, u.role as author_role
                    FROM blogs b 
                    LEFT JOIN users u ON b.user_id = u.id 
                    WHERE b.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result) {
                $result['read_count'] = $this->calculateReadCount($result['created_at'], $id);
                $result['image_path'] = $this->getPostImage($id);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getPostById: " . $e->getMessage());
            return null;
        }
    }
    
    public function createPost($userId, $title, $content) {
        try {
            $sql = "INSERT INTO blogs (user_id, title, content, created_at) 
                    VALUES (?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $title, $content]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Database error in createPost: " . $e->getMessage());
            return false;
        }
    }
    
    public function deletePost($id, $userId = null) {
        try {
            $this->deletePostImage($id);
            
            if ($userId !== null) {
                $sql = "DELETE FROM blogs WHERE id = ? AND user_id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$id, $userId]);
            } else {
                $sql = "DELETE FROM blogs WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$id]);
            }
        } catch (PDOException $e) {
            error_log("Database error in deletePost: " . $e->getMessage());
            return false;
        }
    }
    
    public function getPopularPosts($limit = 3) {
        try {
            $sql = "SELECT b.*, u.name as author_name, u.role as author_role
                    FROM blogs b 
                    LEFT JOIN users u ON b.user_id = u.id 
                    ORDER BY b.created_at DESC 
                    LIMIT " . (int)$limit;
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            
            foreach ($result as &$post) {
                $post['read_count'] = $this->calculateReadCount($post['created_at'], $post['id']);
                $post['image_path'] = $this->getPostImage($post['id']);
            }
            
            usort($result, function($a, $b) {
                return $b['read_count'] - $a['read_count'];
            });
            
            return $result ?: [];
        } catch (PDOException $e) {
            error_log("Database error in getPopularPosts: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPostCount() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM blogs");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Database error in getPostCount: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getAuthorCount() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT user_id) as total FROM blogs");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Database error in getAuthorCount: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getTotalReads() {
        try {
            $stmt = $this->db->prepare("SELECT id, created_at FROM blogs");
            $stmt->execute();
            $posts = $stmt->fetchAll();
            
            $totalReads = 0;
            foreach ($posts as $post) {
                $totalReads += $this->calculateReadCount($post['created_at'], $post['id']);
            }
            
            return $totalReads;
        } catch (PDOException $e) {
            error_log("Database error in getTotalReads: " . $e->getMessage());
            return 0;
        }
    }
    
    private function calculateReadCount($createdAt, $postId) {
        $createdDate = new DateTime($createdAt);
        $now = new DateTime();
        $daysDiff = $now->diff($createdDate)->days;
        
        $baseReads = 50;
        $dailyIncrease = 8;
        $idBonus = $postId % 100;
        
        $readCount = $baseReads + ($daysDiff * $dailyIncrease) + $idBonus;
        
        return min($readCount, 5000);
    }
    
    private function getPostImage($postId) {
        $uploadDir = __DIR__ . '/../assets/uploads/blog/';
        
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        foreach ($extensions as $ext) {
            $imagePath = $uploadDir . 'post_' . $postId . '.' . $ext;
            if (file_exists($imagePath)) {
                return 'assets/uploads/blog/post_' . $postId . '.' . $ext;
            }
        }
        
        return null;
    }
    
    public function savePostImage($postId, $uploadedFile) {
        $uploadDir = __DIR__ . '/../assets/uploads/blog/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileInfo = pathinfo($uploadedFile['name']);
        $extension = strtolower($fileInfo['extension']);
        
        $finalPath = $uploadDir . 'post_' . $postId . '.' . $extension;
        
        return move_uploaded_file($uploadedFile['tmp_name'], $finalPath);
    }
    
    private function deletePostImage($postId) {
        $uploadDir = __DIR__ . '/../assets/uploads/blog/';
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        foreach ($extensions as $ext) {
            $imagePath = $uploadDir . 'post_' . $postId . '.' . $ext;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }
}
?>
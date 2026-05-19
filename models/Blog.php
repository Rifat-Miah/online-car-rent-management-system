<?php
require_once __DIR__ . '/database.php';

class Blog {
    private $db;
    
    public function __construct() {
        $this->db = connectDB();
    }
    
    public function getAllPosts($limit = null, $offset = null, $search = '') {
        try {
            $sql = "SELECT b.*, u.name as author_name, u.role as author_role
                    FROM blogs b 
                    LEFT JOIN users u ON b.user_id = u.id";
            
            if (!empty($search)) {
                $sql .= " WHERE (LOWER(b.title) LIKE LOWER('%{$search}%') OR LOWER(b.content) LIKE LOWER('%{$search}%'))";
            }
            
            $sql .= " ORDER BY b.created_at DESC";
            
            if ($limit !== null && $offset !== null) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            } elseif ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit;
            }
            
            $result = mysqli_query($this->db, $sql);
            
            if (!$result) {
                return [];
            }
            
            $posts = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $row['read_count'] = $this->calculateReadCount($row['created_at'], $row['id']);
                $row['image_path'] = $this->getPostImage($row['id']);
                $posts[] = $row;
            }
            
            return $posts;
        } catch (Exception $e) {
            error_log("Database error in getAllPosts: " . $e->getMessage());
            return [];
        }
    }
    
    public function getTotalCount($search = '') {
        $sql = "SELECT COUNT(*) as total FROM blogs";
        
        if (!empty($search)) {
            $sql .= " WHERE (LOWER(title) LIKE LOWER('%{$search}%') OR LOWER(content) LIKE LOWER('%{$search}%'))";
        }
        
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            return 0;
        }
        
        $row = mysqli_fetch_assoc($result);
        return $row ? (int)$row['total'] : 0;
    }
    
    public function getPostById($id) {
        $id = (int)$id;
        $sql = "SELECT b.*, u.name as author_name, u.role as author_role
                FROM blogs b 
                LEFT JOIN users u ON b.user_id = u.id 
                WHERE b.id = $id";
        
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            return null;
        }
        
        $post = mysqli_fetch_assoc($result);
        
        if ($post) {
            $post['read_count'] = $this->calculateReadCount($post['created_at'], $id);
            $post['image_path'] = $this->getPostImage($id);
        }
        
        return $post;
    }
    
    public function createPost($userId, $title, $content) {
        $userId = (int)$userId;
        $title = mysqli_real_escape_string($this->db, $title);
        $content = mysqli_real_escape_string($this->db, $content);
        
        $sql = "INSERT INTO blogs (user_id, title, content, created_at) 
                VALUES ($userId, '$title', '$content', NOW())";
        
        if (mysqli_query($this->db, $sql)) {
            return mysqli_insert_id($this->db);
        }
        
        return false;
    }
    
    public function deletePost($id, $userId = null) {
        $id = (int)$id;
        $this->deletePostImage($id);
        
        if ($userId !== null) {
            $userId = (int)$userId;
            $sql = "DELETE FROM blogs WHERE id = $id AND user_id = $userId";
        } else {
            $sql = "DELETE FROM blogs WHERE id = $id";
        }
        
        return mysqli_query($this->db, $sql);
    }
    
    public function getPopularPosts($limit = 3) {
        $limit = (int)$limit;
        $sql = "SELECT b.*, u.name as author_name, u.role as author_role
                FROM blogs b 
                LEFT JOIN users u ON b.user_id = u.id 
                ORDER BY b.created_at DESC 
                LIMIT $limit";
        
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            return [];
        }
        
        $posts = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['read_count'] = $this->calculateReadCount($row['created_at'], $row['id']);
            $row['image_path'] = $this->getPostImage($row['id']);
            $posts[] = $row;
        }
        
        usort($posts, function($a, $b) {
            return $b['read_count'] - $a['read_count'];
        });
        
        return $posts;
    }
    
    public function getPostCount() {
        $sql = "SELECT COUNT(*) as total FROM blogs";
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            return 0;
        }
        
        $row = mysqli_fetch_assoc($result);
        return $row ? (int)$row['total'] : 0;
    }
    
    public function getAuthorCount() {
        $sql = "SELECT COUNT(DISTINCT user_id) as total FROM blogs";
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            return 0;
        }
        
        $row = mysqli_fetch_assoc($result);
        return $row ? (int)$row['total'] : 0;
    }
    
    public function getTotalReads() {
        $sql = "SELECT id, created_at FROM blogs";
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            return 0;
        }
        
        $totalReads = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $totalReads += $this->calculateReadCount($row['created_at'], $row['id']);
        }
        
        return $totalReads;
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
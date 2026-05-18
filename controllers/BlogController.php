<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Blog.php';

class BlogController {
    private $blogModel;
    
    public function __construct() {
        $this->blogModel = new Blog();
    }
    
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $limit = 4;
        $offset = ($page - 1) * $limit;
        
        $posts = $this->blogModel->getAllPosts($limit, $offset, $search);
        $totalPosts = $this->blogModel->getTotalCount($search);
        $totalPages = ($totalPosts > 0) ? ceil($totalPosts / $limit) : 1;
        
        $postCount = $this->blogModel->getPostCount();
        $authorCount = $this->blogModel->getAuthorCount();
        $totalReads = $this->blogModel->getTotalReads();
        $popularPosts = $this->blogModel->getPopularPosts(3);
        
        $posts = is_array($posts) ? $posts : [];
        $totalPosts = is_numeric($totalPosts) ? $totalPosts : 0;
        $totalPages = is_numeric($totalPages) ? $totalPages : 1;
        $postCount = is_numeric($postCount) ? $postCount : 0;
        $authorCount = is_numeric($authorCount) ? $authorCount : 0;
        $totalReads = is_numeric($totalReads) ? $totalReads : 0;
        $popularPosts = is_array($popularPosts) ? $popularPosts : [];
        $page = is_numeric($page) ? $page : 1;
        
        include_once __DIR__ . '/../views/blog/index.php';
    }
    
    public function viewPost() {
        $postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($postId <= 0) {
            header('Location: index.php');
            exit;
        }
        
        $post = $this->blogModel->getPostById($postId);
        
        if (!$post) {
            header('Location: index.php');
            exit;
        }
        
        include_once __DIR__ . '/../views/blog/blogDetails.php';
    }
    
    public function getPosts() {
        header('Content-Type: application/json');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $limit = 4;
        $offset = ($page - 1) * $limit;
        
        $posts = $this->blogModel->getAllPosts($limit, $offset, $search);
        $totalPosts = $this->blogModel->getTotalCount($search);
        $totalPages = ($totalPosts > 0) ? ceil($totalPosts / $limit) : 1;
        
        $postsArray = [];
        if ($posts && is_array($posts)) {
            foreach ($posts as $post) {
                $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
                $isOwner = isset($_SESSION['user_id']) && $post['user_id'] == $_SESSION['user_id'];
                $canDelete = $isAdmin || $isOwner;
                
                $postsArray[] = [
                    'id' => $post['id'],
                    'title' => htmlspecialchars($post['title']),
                    'content' => htmlspecialchars(substr($post['content'], 0, 150)) . '...',
                    'author_name' => htmlspecialchars($post['author_name']),
                    'created_at' => date('M j, Y', strtotime($post['created_at'])),
                    'author_initial' => substr($post['author_name'], 0, 1) . substr($post['author_name'], -1, 1),
                    'can_delete' => $canDelete,
                    'author_role' => $post['author_role'] ?? 'member',
                    'image_path' => $post['image_path'],
                    'read_count' => $post['read_count']
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'posts' => $postsArray,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalPosts' => $totalPosts,
            'searchTerm' => $search
        ]);
    }
    
    public function create() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Please login to create a post']);
            exit;
        }
        
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        $errors = [];
        
        if (empty($title)) {
            $errors['title'] = 'Title is required';
        } elseif (strlen($title) < 5) {
            $errors['title'] = 'Title must be at least 5 characters';
        } elseif (strlen($title) > 200) {
            $errors['title'] = 'Title must be less than 200 characters';
        }
        
        if (empty($content)) {
            $errors['content'] = 'Content is required';
        } elseif (strlen($content) < 20) {
            $errors['content'] = 'Content must be at least 20 characters';
        }
        
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }
        
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        
        $postId = $this->blogModel->createPost($_SESSION['user_id'], $title, $content);
        
        if ($postId) {
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $maxFileSize = 5 * 1024 * 1024;
                
                $fileInfo = pathinfo($_FILES['cover_image']['name']);
                $extension = strtolower($fileInfo['extension']);
                
                if (in_array($extension, $allowedExtensions) && $_FILES['cover_image']['size'] <= $maxFileSize) {
                    $this->blogModel->savePostImage($postId, $_FILES['cover_image']);
                }
            }
            
            $postCount = $this->blogModel->getPostCount();
            $authorCount = $this->blogModel->getAuthorCount();
            $totalReads = $this->blogModel->getTotalReads();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Post created successfully!',
                'stats' => [
                    'postCount' => $postCount,
                    'authorCount' => $authorCount,
                    'totalReads' => $totalReads
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create post']);
        }
    }
    
    public function delete() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Please login to delete posts']);
            exit;
        }
        
        $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
        
        if ($postId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
            exit;
        }
        
        $post = $this->blogModel->getPostById($postId);
        
        if (!$post) {
            echo json_encode(['success' => false, 'message' => 'Post not found']);
            exit;
        }
        
        $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
        $isOwner = $post['user_id'] == $_SESSION['user_id'];
        
        if (!$isAdmin && !$isOwner) {
            echo json_encode(['success' => false, 'message' => 'You can only delete your own posts']);
            exit;
        }
        
        if ($this->blogModel->deletePost($postId, $isAdmin ? null : $_SESSION['user_id'])) {
            $postCount = $this->blogModel->getPostCount();
            $authorCount = $this->blogModel->getAuthorCount();
            $totalReads = $this->blogModel->getTotalReads();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Post deleted successfully',
                'stats' => [
                    'postCount' => $postCount,
                    'authorCount' => $authorCount,
                    'totalReads' => $totalReads
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
        }
    }
    
    public function getStats() {
        header('Content-Type: application/json');
        
        $postCount = $this->blogModel->getPostCount();
        $authorCount = $this->blogModel->getAuthorCount();
        $totalReads = $this->blogModel->getTotalReads();
        
        echo json_encode([
            'success' => true,
            'postCount' => $postCount ?: 0,
            'authorCount' => $authorCount ?: 0,
            'totalReads' => $totalReads ?: 0
        ]);
    }
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $controller = new BlogController();
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $controller->create();
        } elseif ($_POST['action'] === 'delete') {
            $controller->delete();
        }
    }
} elseif ($method === 'GET') {
    $controller = new BlogController();
    
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'getPosts') {
            $controller->getPosts();
        } elseif ($_GET['action'] === 'getStats') {
            $controller->getStats();
        }
    } elseif (isset($_GET['id'])) {
        $controller->viewPost();
    } else {
        $controller->index();
    }
}
?>
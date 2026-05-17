<?php
require_once __DIR__ . '/../models/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php?controller=adminLogin");
    exit();
}

$pdo    = Database::getInstance()->getConnection();
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'addCar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name']);
    $model        = trim($_POST['model']);
    $type         = $_POST['type'];
    $price        = $_POST['price_per_day'];
    $availability = $_POST['availability_status'];
    $description  = trim($_POST['description']);

    $stmt = $pdo->prepare("INSERT INTO cars (name, model, type, price_per_day, availability_status, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $model, $type, $price, $availability, $description]);

    header("Location: index.php?controller=adminDashboard&car_added=1");
    exit();
}

if ($action === 'deleteMember' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'member'");
    $stmt->execute([$member_id]);

    header("Location: index.php?controller=adminDashboard&member_deleted=1");
    exit();
}

if ($action === 'deleteBlog' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_id = $_POST['blog_id'];

    $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->execute([$blog_id]);

    header("Location: index.php?controller=adminDashboard&blog_deleted=1");
    exit();
}

$members = $pdo->query("SELECT * FROM users WHERE role = 'member' ORDER BY created_at DESC")->fetchAll();

$orders = $pdo->query("
    SELECT o.*, u.name as user_name, c.name as car_name, c.model
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN cars  c ON o.car_id  = c.id
    ORDER BY o.id DESC
")->fetchAll();

$blogs = $pdo->query("
    SELECT b.*, u.name as author_name
    FROM blogs b
    JOIN users u ON b.user_id = u.id
    ORDER BY b.created_at DESC
")->fetchAll();

$totalCars    = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
$totalMembers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'member'")->fetchColumn();
$totalOrders  = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalBlogs   = $pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();

require_once __DIR__ . '/../views/adminDashboard.php';
?>
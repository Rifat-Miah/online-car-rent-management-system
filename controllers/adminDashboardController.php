<?php
require_once __DIR__ . '/../models/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php?controller=adminLogin");
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'addCar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name']);
    $model        = trim($_POST['model']);
    $type         = $_POST['type'];
    $price        = $_POST['price_per_day'];
    $availability = $_POST['availability_status'];
    $description  = trim($_POST['description']);

    $stmt = mysqli_prepare($con, "INSERT INTO cars (name, model, type, price_per_day, availability_status, description) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssdss", $name, $model, $type, $price, $availability, $description);
    mysqli_stmt_execute($stmt);

    header("Location: index.php?controller=adminDashboard&car_added=1");
    exit();
}

if ($action === 'deleteMember' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];

    $stmt = mysqli_prepare($con, "DELETE FROM users WHERE id = ? AND role = 'member'");
    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);

    header("Location: index.php?controller=adminDashboard&member_deleted=1");
    exit();
}

if ($action === 'deleteBlog' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_id = $_POST['blog_id'];

    $stmt = mysqli_prepare($con, "DELETE FROM blogs WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $blog_id);
    mysqli_stmt_execute($stmt);

    header("Location: index.php?controller=adminDashboard&blog_deleted=1");
    exit();
}

$members = mysqli_fetch_all(
    mysqli_query($con, "SELECT * FROM users WHERE role = 'member' ORDER BY created_at DESC"),
    MYSQLI_ASSOC
);

$orders = mysqli_fetch_all(
    mysqli_query($con, "
        SELECT o.*, u.name as user_name, c.name as car_name, c.model
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN cars  c ON o.car_id  = c.id
        ORDER BY o.id DESC
    "),
    MYSQLI_ASSOC
);

$blogs = mysqli_fetch_all(
    mysqli_query($con, "
        SELECT b.*, u.name as author_name
        FROM blogs b
        JOIN users u ON b.user_id = u.id
        ORDER BY b.created_at DESC
    "),
    MYSQLI_ASSOC
);

$totalCars    = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM cars"))[0];
$totalMembers = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM users WHERE role = 'member'"))[0];
$totalOrders  = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM orders"))[0];
$totalBlogs   = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM blogs"))[0];

require_once __DIR__ . '/../views/adminDashboard.php';
?>
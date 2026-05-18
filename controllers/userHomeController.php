<?php
require_once __DIR__ . '/../models/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?controller=login");
    exit();
}

$userRole = $_SESSION['user_role'] ?? '';
if ($userRole === 'admin') {
    header("Location: index.php?controller=adminDashboard");
    exit();
}

$type = isset($_GET['type']) ? trim($_GET['type']) : '';

if ($type !== '') {
    $stmt = mysqli_prepare($con, "
        SELECT * FROM cars
        WHERE availability_status = 'available' AND type = ?
        ORDER BY id DESC
        LIMIT 6
    ");
    mysqli_stmt_bind_param($stmt, "s", $type);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $featuredCars = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $featuredCars = mysqli_fetch_all(
        mysqli_query($con, "
            SELECT * FROM cars
            WHERE availability_status = 'available'
            ORDER BY RAND()
            LIMIT 6
        "),
        MYSQLI_ASSOC
    );
}

$carTypes = mysqli_fetch_all(
    mysqli_query($con, "SELECT DISTINCT type FROM cars ORDER BY type ASC"),
    MYSQLI_ASSOC
);

$carTypes = array_map(function ($row) {
    return $row['type'];
}, $carTypes);

require_once __DIR__ . '/../views/userHome.php';
?>
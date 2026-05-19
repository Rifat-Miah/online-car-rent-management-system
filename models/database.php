<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'CarRentDb');
define('DB_USER', 'root');
define('DB_PASS', '');

function connectDB() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}

$con = connectDB();
?>
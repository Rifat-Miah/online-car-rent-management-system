<?php
require_once 'models/database.php';

function getAllAvailableCars() {
    global $con;
    
    $result = mysqli_query($con, "SELECT * FROM cars WHERE availability_status = 'available'");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getCarById($id) {
    global $con;
    
    $stmt = mysqli_prepare($con, "SELECT * FROM cars WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
?>
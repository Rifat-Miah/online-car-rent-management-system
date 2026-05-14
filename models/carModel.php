<?php
require_once __DIR__ . '/database.php';

function getAllCars() {
    global $con;

    $result = mysqli_query($con, "SELECT * FROM cars");

    $cars = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }

    return $cars;
}

function getCarById($id) {
    global $con;

    $result = mysqli_query($con, "SELECT * FROM cars WHERE id = '$id'");

    return mysqli_fetch_assoc($result);
}

function createCar($name, $model, $type, $price, $image, $availability_status) {
    global $con;

    $query = "INSERT INTO cars 
    (name, model, type, price, image, availability_status)
    VALUES
    ('$name', '$model', '$type', '$price', '$image', '$availability_status')";

    return mysqli_query($con, $query);
}

function updateCar($id, $name, $model, $type, $price, $image, $availability_status) {
    global $con;

    $query = "UPDATE cars SET
    name='$name',
    model='$model',
    type='$type',
    price='$price',
    image='$image',
    availability_status='$availability_status'
    WHERE id='$id'";

    return mysqli_query($con, $query);
}

function deleteCar($id) {
    global $con;

    return mysqli_query($con, "DELETE FROM cars WHERE id='$id'");
}

function searchCars($find) {
    global $con;

    $query = "SELECT * FROM cars 
    WHERE name LIKE '%$find%' 
    OR model LIKE '%$find%' 
    OR type LIKE '%$find%'";

    $result = mysqli_query($con, $query);

    $cars = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }

    return $cars;
}
?>
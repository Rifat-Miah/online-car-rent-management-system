<?php
require_once __DIR__ . '/database.php';

function getUserByEmail($email) {
    global $con;

    $result = mysqli_query($con, 
    "SELECT * FROM users WHERE email = '$email'");

    return mysqli_fetch_assoc($result);
}

function getUserById($id) {
    global $con;

    $result = mysqli_query($con, 
    "SELECT * FROM users WHERE id = '$id'");

    return mysqli_fetch_assoc($result);
}

function getUserByRememberToken($token) {
    global $con;

    $result = mysqli_query($con, 
    "SELECT * FROM users WHERE remember_token = '$token'");

    return mysqli_fetch_assoc($result);
}

function createUser($name, $email, $password, $role, $address, $phone) {
    global $con;

    $query = "INSERT INTO users
    (name, email, password_hash, role, address, phone)
    VALUES
    ('$name', '$email', '$password', '$role', '$address', '$phone')";

    return mysqli_query($con, $query);
}

function updateUser($id, $name, $email, $address, $phone, $profile_picture) {
    global $con;

    $query = "UPDATE users SET
    name='$name',
    email='$email',
    address='$address',
    phone='$phone',
    profile_picture='$profile_picture'
    WHERE id='$id'";

    return mysqli_query($con, $query);
}

function updatePassword($id, $newpass) {
    global $con;

    $query = "UPDATE users SET
    password_hash='$newpass'
    WHERE id='$id'";

    return mysqli_query($con, $query);
}

function setRememberToken($id, $token) {
    global $con;

    $query = "UPDATE users SET
    remember_token='$token'
    WHERE id='$id'";

    return mysqli_query($con, $query);
}

function clearRememberToken($id) {
    global $con;

    $query = "UPDATE users SET
    remember_token=NULL
    WHERE id='$id'";

    return mysqli_query($con, $query);
}
?>
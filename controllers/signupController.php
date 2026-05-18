<?php
require_once __DIR__ . '/../models/database.php';

$error = '';
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $address  = trim($_POST['address']);
    $phone    = trim($_POST['phone']);
    $role     = $_POST['role'];

    $old = compact('name', 'email', 'address', 'phone', 'role');

    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (!in_array($role, ['admin', 'member'])) {
        $error = "Invalid role selected.";
    } else {

        $stmt = mysqli_prepare($con, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Email already registered. Please use another.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = mysqli_prepare($con, "INSERT INTO users (name, email, password_hash, address, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $hashedPassword, $address, $phone, $role);
            mysqli_stmt_execute($stmt);

           if ($role === 'admin') {
              header("Location: index.php?controller=adminLogin&registered=1");
           } else {
               header("Location: index.php?controller=login&registered=1");
               }
            exit();
        }
    }
}

require_once __DIR__ . '/../views/signup.php';
?>
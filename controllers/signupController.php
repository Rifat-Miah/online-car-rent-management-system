<?php
require_once __DIR__ . '/../models/database.php';

$pdo = Database::getInstance()->getConnection();

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
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email already registered. Please use another.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, address, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword, $address, $phone, $role]);

            header("Location: index.php?controller=login&registered=1");
            exit();
        }
    }
}

require_once __DIR__ . '/../views/signup.php';
?>
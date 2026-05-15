<?php
require_once __DIR__ . '/../models/database.php';

$error = '';
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $old = ['email' => $email];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $pdo = Database::getInstance()->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {

            if ($user['role'] !== 'admin') {
                $error = "Access denied. You are not an admin.";
            } else {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: index.php?controller=adminDashboard");
                exit();
            }

        } else {
            $error = "Invalid email or password.";
        }
    }
}

require_once __DIR__ . '/../views/adminLogin.php';
?>
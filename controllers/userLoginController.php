<?php
require_once __DIR__ . '/../models/database.php';

$error = '';
$old = [];

if (empty($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE remember_token = ?");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user && $user['role'] === 'member') {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        header("Location: index.php?controller=userDashboard");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $old = ['email' => $email];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {

        $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($user && password_verify($password, $user['password_hash'])) {

            if ($user['role'] !== 'member') {
                $error = "Access denied. Please use Admin Login.";
            } else {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                if ($remember) {
                    $token = bin2hex(random_bytes(32));

                    $stmt = mysqli_prepare($con, "UPDATE users SET remember_token = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "si", $token, $user['id']);
                    mysqli_stmt_execute($stmt);

                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                }

                header("Location: index.php?controller=userDashboard");
                exit();
            }

        } else {
            $error = "Invalid email or password.";
        }
    }
}

require_once __DIR__ . '/../views/userLogin.php';
?>
<?php
require_once __DIR__ . '/../models/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php?controller=adminLogin");
    exit();
}

$success = '';
$error   = '';

if (isset($_POST['update_profile'])) {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone   = trim($_POST['phone']);
    $id      = $_SESSION['user_id'];

    $stmt = mysqli_prepare($con, "SELECT id FROM users WHERE email = ? AND id != ?");
    mysqli_stmt_bind_param($stmt, "si", $email, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $error = "Email already used by another account.";
    } else {
        $picQuery = "";
        $picPath  = null;

        if (!empty($_FILES['profile_picture']['name'])) {
            $ext      = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $allowed  = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array(strtolower($ext), $allowed)) {
                $error = "Only JPG, PNG, GIF allowed.";
            } else {
                $newName = "profile_" . $id . "_" . time() . "." . $ext;
                $dest    = __DIR__ . '/../uploads/' . $newName;
                move_uploaded_file($_FILES['profile_picture']['tmp_name'], $dest);
                $picPath = $newName;
            }
        }

        if (empty($error)) {
            if ($picPath) {
                $stmt = mysqli_prepare($con, "UPDATE users SET name=?, email=?, address=?, phone=?, profile_picture=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $address, $phone, $picPath, $id);
            } else {
                $stmt = mysqli_prepare($con, "UPDATE users SET name=?, email=?, address=?, phone=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $address, $phone, $id);
            }
            mysqli_stmt_execute($stmt);
            $_SESSION['user_name'] = $name;
            $success = "Profile updated successfully!";
        }
    }
}

if (isset($_POST['change_password'])) {
    $current  = $_POST['current_password'];
    $new      = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];
    $id       = $_SESSION['user_id'];

    $stmt = mysqli_prepare($con, "SELECT password_hash FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!password_verify($current, $row['password_hash'])) {
        $error = "Current password is incorrect.";
    } elseif (strlen($new) < 8) {
        $error = "New password must be at least 8 characters.";
    } elseif ($new !== $confirm) {
        $error = "New passwords do not match.";
    } else {
        $hashed = password_hash($new, PASSWORD_BCRYPT);
        $stmt   = mysqli_prepare($con, "UPDATE users SET password_hash = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hashed, $id);
        mysqli_stmt_execute($stmt);
        $success = "Password changed successfully!";
    }
}

$stmt = mysqli_prepare($con, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

require_once __DIR__ . '/../views/adminProfile.php';
?>
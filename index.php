<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'homepage';

if ($controller === 'homepage') {
    require_once __DIR__ . '/controllers/homepazeController.php';
} elseif ($controller === 'signup') {
    require_once __DIR__ . '/controllers/signupController.php';
} elseif ($controller === 'login') {
    require_once __DIR__ . '/controllers/userLoginController.php';
} elseif ($controller === 'userHome') {
    require_once __DIR__ . '/controllers/userHomeController.php';
}
 elseif ($controller === 'adminLogin') {
    require_once __DIR__ . '/controllers/adminLoginController.php';
} elseif ($controller === 'adminProfile') {
     require_once __DIR__ . '/controllers/adminProfileController.php';
}
 elseif ($controller === 'userProfile') {
    require_once __DIR__ . '/controllers/userProfileController.php';
} elseif ($controller === 'userLogout') {
    setcookie('remember_token', '', time() - 3600, '/');
    session_destroy();
    header("Location: index.php");
    exit();
} elseif ($controller === 'adminLogout') {
    setcookie('admin_remember_token', '', time() - 3600, '/');
    session_destroy();
    header("Location: index.php");
    exit();
}  
elseif ($controller === 'blog') {
    require_once __DIR__ . '/controllers/BlogController.php';
} else {
    require_once __DIR__ . '/controllers/homepazeController.php';
}
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'homepage';

if ($controller === 'homepage') {
    require_once __DIR__ . '/controllers/homepazeController.php';
} elseif ($controller === 'signup') {
    require_once __DIR__ . '/controllers/signupController.php';
} elseif ($controller === 'blog') {
    require_once __DIR__ . '/controllers/BlogController.php';
} else {
    require_once __DIR__ . '/controllers/homepazeController.php';
}
?>
<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'blog';

if ($controller === 'blog') {
    require_once __DIR__ . '/controllers/BlogController.php';
} else {
    require_once __DIR__ . '/controllers/BlogController.php';
}
?>
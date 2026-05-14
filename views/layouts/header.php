<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DriveGo - Your trusted car rental partner for unforgettable road trips">
    <meta name="author" content="DriveGo Team">
    <title>DriveGo - Car Rental</title>
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    
    <!-- Blog CSS - Relative path -->
    <link rel="stylesheet" href="assets/css/blog.css">
    
</head>
<body>

<?php
// Get current controller for active class
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'blog';
?>

<nav class="nav">
    <div class="nav-logo">DRIVE<span>GO</span></div>
    <div class="nav-links">
        <a href="index.php?controller=home" class="<?php echo ($controller == 'home') ? 'active' : ''; ?>">Home</a>
        
        <a href="#" class="<?php echo ($controller == 'rates') ? 'active' : ''; ?>">Rates</a>
        <a href="index.php?controller=blog" class="<?php echo ($controller == 'blog') ? 'active' : ''; ?>">Blog</a>
        <a href="#" class="<?php echo ($controller == 'contact') ? 'active' : ''; ?>">Contact</a>
    </div>
    <div class="auth-buttons" id="authButtons">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <span class="user-role">(<?php echo htmlspecialchars($_SESSION['user_role']); ?>)</span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        <?php else: ?>
            <a href="views/auth/login.php" class="login-btn">Login</a>
            <a href="views/auth/register.php" class="signup-btn">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>

<!-- MAIN CONTENT STARTS HERE -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Online Car Rent</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">

    <aside class="sidebar">
        <h2>Admin Panel</h2>

        <a href="index.php?controller=adminDashboard">Dashboard</a>
        <a href="index.php?controller=adminCars">Car Management</a>
        <a href="index.php?controller=adminMembers">Members</a>
        <a href="index.php?controller=adminOrders">Rent Orders</a>
        <a href="index.php?controller=blog">Blog</a>
    </aside>

    <main class="main-content">
        <h1>Admin Dashboard</h1>
        <p class="subtitle">Overview of the online car rent system</p>

        <div class="card-grid">
            <div class="card">
                <h3>Total Cars</h3>
                <p><?php echo htmlspecialchars($totalCars); ?></p>
            </div>

            <div class="card">
                <h3>Total Members</h3>
                <p><?php echo htmlspecialchars($totalMembers); ?></p>
            </div>

            <div class="card">
                <h3>Total Orders</h3>
                <p><?php echo htmlspecialchars($totalOrders); ?></p>
            </div>

            <div class="card">
                <h3>Total Blog Posts</h3>
                <p><?php echo htmlspecialchars($totalBlogs); ?></p>
            </div>
        </div>
    </main>

</div>

</body>
</html>
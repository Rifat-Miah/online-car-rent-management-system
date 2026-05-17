<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Management - Online Car Rent</title>
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
    </aside>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1>Car Management</h1>
                <p class="subtitle">View all car listings</p>
            </div>

            <a class="btn-primary" href="index.php?controller=adminCars&action=createCar">
                Add New Car
            </a>
        </div>

        <?php if (!empty($_GET['success']) && $_GET['success'] === 'created'): ?>
            <div class="alert-success">Car added successfully.</div>
        <?php endif; ?>

        <?php if (!empty($_GET['success']) && $_GET['success'] === 'updated'): ?>
            <div class="alert-success">Car updated successfully.</div>
        <?php endif; ?>

        <div class="table-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Car Name</th>
                        <th>Model</th>
                        <th>Type</th>
                        <th>Price/Day</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($cars)): ?>
                        <?php foreach ($cars as $car): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($car['id']); ?></td>
                                <td>
                                    <?php if (!empty($car['image_path'])): ?>
                                        <img class="car-thumb" src="<?php echo htmlspecialchars($car['image_path']); ?>" alt="Car Image">
                                    <?php else: ?>
                                        No image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($car['name']); ?></td>
                                <td><?php echo htmlspecialchars($car['model']); ?></td>
                                <td><?php echo htmlspecialchars($car['type']); ?></td>
                                <td><?php echo htmlspecialchars($car['price_per_day']); ?></td>
                                <td><?php echo htmlspecialchars($car['availability_status']); ?></td>
                                <td><?php echo htmlspecialchars($car['description'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($car['created_at']); ?></td>
                                <td>
                                    <a class="btn-small" href="index.php?controller=adminCars&action=editCar&id=<?php echo $car['id']; ?>">Edit</a>
                                    <a class="btn-small btn-danger" href="index.php?controller=adminCars&action=deleteCar&id=<?php echo $car['id']; ?>">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">No cars found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</div>

</body>
</html>
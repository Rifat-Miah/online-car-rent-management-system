<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rent Order History - Online Car Rent</title>
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
                <h1>Rent Order History</h1>
                <p class="subtitle">View all rent orders placed by members</p>
            </div>
        </div>

        <div class="table-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Member</th>
                        <th>Email</th>
                        <th>Car</th>
                        <th>Model</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                        <th>Payment Method</th>
                        <th>Order Date</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['member_name'] ?? 'Deleted Member'); ?></td>
                                <td><?php echo htmlspecialchars($order['member_email'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($order['car_name'] ?? 'Deleted Car'); ?></td>
                                <td><?php echo htmlspecialchars($order['car_model'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($order['car_type'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($order['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['end_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['total_cost']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars($order['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center">No rent orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</div>

</body>
</html>
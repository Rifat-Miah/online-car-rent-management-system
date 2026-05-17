<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Members - Online Car Rent</title>
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
                <h1>Member Management</h1>
                <p class="subtitle">View and remove registered members</p>
            </div>
        </div>

        <div id="memberMessage"></div>

        <div class="table-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($members)): ?>
                        <?php foreach ($members as $member): ?>
                            <tr id="member-row-<?php echo htmlspecialchars($member['id']); ?>">
                                <td><?php echo htmlspecialchars($member['id']); ?></td>
                                <td><?php echo htmlspecialchars($member['name']); ?></td>
                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                <td><?php echo htmlspecialchars($member['phone'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($member['address'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($member['created_at']); ?></td>
                                <td>
                                    <button 
                                        type="button"
                                        class="btn-small btn-danger delete-member-btn"
                                        data-id="<?php echo htmlspecialchars($member['id']); ?>"
                                        data-token="<?php echo htmlspecialchars($csrfToken); ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</div>

<script src="assets/js/admin.js"></script>

</body>
</html>
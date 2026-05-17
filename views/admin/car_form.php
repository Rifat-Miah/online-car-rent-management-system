<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($formTitle); ?> - Online Car Rent</title>
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
                <h1><?php echo htmlspecialchars($formTitle); ?></h1>
                <p class="subtitle">Fill in the car details carefully</p>
            </div>

            <a class="btn-primary" href="index.php?controller=adminCars">Back to Cars</a>
        </div>

        <div class="form-card">
            <?php if (!empty($errors['general'])): ?>
                <div class="alert-error">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($formAction); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

                <div class="form-group">
                    <label>Car Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($car['name']); ?>">
                    <?php if (!empty($errors['name'])): ?>
                        <small class="error-text"><?php echo htmlspecialchars($errors['name']); ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Model</label>
                    <input type="text" name="model" value="<?php echo htmlspecialchars($car['model']); ?>">
                    <?php if (!empty($errors['model'])): ?>
                        <small class="error-text"><?php echo htmlspecialchars($errors['model']); ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Car Type</label>
                    <select name="type">
                        <option value="">Select Type</option>
                        <?php
                        $types = ['Private car', 'Microbus', 'PickUp', 'SUV', 'Van', 'Sedan'];
                        foreach ($types as $type):
                        ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" 
                                <?php echo ($car['type'] === $type) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['type'])): ?>
                        <small class="error-text"><?php echo htmlspecialchars($errors['type']); ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Price Per Day</label>
                    <input type="number" step="0.01" min="1" name="price_per_day" value="<?php echo htmlspecialchars($car['price_per_day']); ?>">
                    <?php if (!empty($errors['price_per_day'])): ?>
                        <small class="error-text"><?php echo htmlspecialchars($errors['price_per_day']); ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Availability Status</label>
                    <select name="availability_status">
                        <option value="available" <?php echo ($car['availability_status'] === 'available') ? 'selected' : ''; ?>>Available</option>
                        <option value="unavailable" <?php echo ($car['availability_status'] === 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                    </select>
                    <?php if (!empty($errors['availability_status'])): ?>
                        <small class="error-text"><?php echo htmlspecialchars($errors['availability_status']); ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
    <label>Car Image</label>

    <?php if (!empty($car['image_path'])): ?>
        <div class="current-image-box">
            <p>Current Image:</p>
            <img class="current-car-image" src="<?php echo htmlspecialchars($car['image_path']); ?>" alt="Current Car Image">
        </div>
    <?php endif; ?>

    <input type="file" name="image" accept="image/jpeg,image/png">
    <small>Allowed: JPG or PNG, maximum 2MB.</small>

    <?php if (!empty($errors['image'])): ?>
        <small class="error-text"><?php echo htmlspecialchars($errors['image']); ?></small>
    <?php endif; ?>
</div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($car['description']); ?></textarea>
                </div>

                <button type="submit" class="btn-submit">Save Car</button>
            </form>
        </div>
    </main>

</div>

</body>
</html>
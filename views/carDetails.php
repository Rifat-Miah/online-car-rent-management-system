<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Rent <?= htmlspecialchars($car['name']) ?> - Online Car Rent</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="container">
        <h1><?= htmlspecialchars($car['name']) ?></h1>
        <div class="car-model"><?= htmlspecialchars($car['model']) ?></div>

        <div class="car-image-box">
            <?php if (!empty($car['image_path'])): ?>
                <img src="assets/public/uploads/cars/<?= htmlspecialchars($car['image_path']) ?>" alt="<?= htmlspecialchars($car['name']) ?>">
            <?php else: ?>
                <span class="no-image">No Image Available</span>
            <?php endif; ?>
        </div>

        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Category Type:</span>
                <span class="info-value"><?= htmlspecialchars($car['type']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Rate Per Day:</span>
                <span class="info-value">BDT <?= number_format($car['price_per_day'], 2) ?></span>
            </div>
            <?php if (!empty($car['description'])): ?>
                <div class="car-description">
                    <strong>Description:</strong> <?= htmlspecialchars($car['description']) ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="index.php?controller=order&action=placeOrder" method="POST" id="orderForm">
            <input type="hidden" name="car_id" id="car_id" value="<?= (int)$car['id'] ?>">

            <label for="start_date">Rental Start Date</label>
            <input type="date" name="start_date" id="start_date" required>

            <label for="end_date">Rental End Date</label>
            <input type="date" name="end_date" id="end_date" required>

            <div id="costPreview">Select matching dates to compute total cost.</div>

            <button type="submit" id="submitBtn" disabled>Proceed to Invoice</button>
        </form>

        <a href="index.php?controller=cars" class="back-link">← Choose an alternative vehicle</a>
    </div>

    <script src="assets/js/order.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Cars - Online Car Rent</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <h2 class="page-title">Available Cars to Rent</h2>

    <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
        
        <?php foreach($cars as $car): ?>
            <div class="car-card">
                <h3><?= htmlspecialchars($car['name']) ?> <?= htmlspecialchars($car['model']) ?></h3>
                <p><strong>Type:</strong> <?= htmlspecialchars($car['type']) ?></p>
                <p><strong>Price:</strong> BDT <?= number_format($car['price_per_day'], 2) ?> / day</p>
                
                <a href="index.php?controller=order&action=view&car_id=<?= (int)$car['id'] ?>" 
                   style="display: block; margin-top: 10px; background: blue; color: white; padding: 8px; text-align: center; text-decoration: none; border-radius: 4px; font-weight: bold;">
                   Rent Now
                </a>
            </div>
        <?php endforeach; ?>

    </div>

</body>
</html>

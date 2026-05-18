
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Car Rent</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<div style="font-family:Arial; padding:20px;">
    <h2>My Rental History</h2>
    <table style="width:100%; border-collapse:collapse; margin-top:15px;">
        <tr style="background:#f4f4f4; text-align:left;">
            <th style="padding:10px; border:1px solid #ddd;">Order ID</th>
            <th style="padding:10px; border:1px solid #ddd;">Car</th>
            <th style="padding:10px; border:1px solid #ddd;">Dates</th>
            <th style="padding:10px; border:1px solid #ddd;">Total</th>
            <th style="padding:10px; border:1px solid #ddd;">Status</th>
        </tr>
        <?php foreach($orders as $order): ?>
        <tr>
            <td style="padding:10px; border:1px solid #ddd;">#<?= $order['id'] ?></td>
            <td style="padding:10px; border:1px solid #ddd;"><?= htmlspecialchars($order['car_name']) ?></td>
            <td style="padding:10px; border:1px solid #ddd;"><?= $order['start_date'] ?> to <?= $order['end_date'] ?></td>
            <td style="padding:10px; border:1px solid #ddd;">BDT <?= $order['total_cost'] ?></td>
            <td style="padding:10px; border:1px solid #ddd;"><strong><?= strtoupper($order['status']) ?></strong></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
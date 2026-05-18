<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Car Rent</title>
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<div style="max-width:400px; margin:20px auto; font-family:Arial; padding:20px; border:1px solid #ccc; border-radius:8px;">
    <h2>Invoice #<?= $order['id'] ?></h2>
    <p><strong>Car:</strong> <?= htmlspecialchars($order['car_name']) ?> <?= htmlspecialchars($order['model']) ?></p>
    <p><strong>Period:</strong> <?= $order['start_date'] ?> to <?= $order['end_date'] ?></p>
    <h3 style="color:green; margin:15px 0;">Total Due: BDT <?= $order['total_cost'] ?></h3>

    <?php if($order['status'] === 'pending'): ?>
        <form action="index.php?controller=invoice&action=pay&id=<?= $order['id'] ?>" method="POST">
            <select name="payment_method" required style="width:100%; padding:10px; margin-bottom:10px;">
                <option value="CreditCard">Credit Card</option>
                <option value="Bkash">bKash</option>
                <option value="Nagad">Nagad</option>
                <option value="BankTransfer">Bank Transfer</option>
            </select>
            <button type="submit" style="width:100%; padding:10px; background:blue; color:white; border:none; cursor:pointer;">Finalize & Pay</button>
        </form>
        <a href="index.php?controller=invoice&action=cancel&id=<?= $order['id'] ?>" style="display:block; text-align:center; margin-top:10px; color:red; text-decoration:none;" onclick="return confirm('Cancel order?');">Cancel Order</a>
    <?php else: ?>
        <p>Status: <strong><?= strtoupper($order['status']) ?></strong></p>
        <a href="index.php?controller=history">Go to Rental History</a>
    <?php endif; ?>
</div>


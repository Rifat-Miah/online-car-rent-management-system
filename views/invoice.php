<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= (int)$order['id'] ?> - Online Car Rent</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .btn-cancel-ajax {
            width: 100%;
            padding: 12px;
            background-color: #d90429;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
            text-align: center;
        }
        .btn-cancel-ajax:hover {
            background-color: #b3001b;
        }
        .invoice-details p {
            margin: 10px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Invoice #<?= (int)$order['id'] ?></h2>
        
        <div class="invoice-details">
            <p><strong>Car:</strong> <?= htmlspecialchars($order['car_name']) ?> <?= htmlspecialchars($order['model']) ?></p>
            <p><strong>Period:</strong> <?= htmlspecialchars($order['start_date']) ?> to <?= htmlspecialchars($order['end_date']) ?></p>
            <h3 style="color: #2d6a4f; margin: 20px 0;">Total Due: BDT <?= number_format($order['total_cost'], 2) ?></h3>
        </div>

        <?php if ($order['status'] === 'pending'): ?>
            <form action="index.php?controller=invoice&action=pay&id=<?= (int)$order['id'] ?>" method="POST">
                <label for="payment_method">Select Payment Method</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="CreditCard">Credit Card</option>
                    <option value="Bkash">bKash</option>
                    <option value="Nagad">Nagad</option>
                    <option value="Banktransfer">Bank Transfer</option>
                    <option value="CashOnDelivery">Cash on Delivery</option>
                </select>
                <button type="submit" class="btn-pay">Finalize & Pay</button>
            </form>
            
            <button type="button" id="ajaxCancelBtn" data-id="<?= (int)$order['id'] ?>" class="btn-cancel-ajax">Cancel Order</button>
            
        <?php else: ?>
            <div style="margin: 20px 0; padding: 10px; background-color: #f8f9fa; border-radius: 5px; text-align: center;">
                <p>Status: <strong style="color: #2d6a4f;"><?= strtoupper(htmlspecialchars($order['status'])) ?></strong></p>
            </div>
            <a href="index.php?controller=history" style="display: block; text-align: center; margin-top: 15px; font-weight: bold;">← Go to Rental History</a>
        <?php endif; ?>
    </div>

    <script>
    const cancelBtn = document.getElementById('ajaxCancelBtn');
    
    if (cancelBtn) {
        cancelBtn.onclick = function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to cancel and delete this order?')) {
                const orderId = cancelBtn.getAttribute('data-id');
                
                fetch('index.php?controller=invoice&action=cancel&id=' + orderId, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        
                        document.querySelector('.container').innerHTML = `
                            <h2 style="color: #d90429; margin-bottom: 15px;">Order Deleted</h2>
                            <p style="margin-bottom: 25px; line-height: 1.6;">Your temporary checkout session has been cleared. <strong>Order #${orderId}</strong> was completely removed from the system.</p>
                            <a href="index.php?controller=cars" class="btn-pay" style="display: block; text-align: center; text-decoration: none; box-sizing: border-box;">Return to Car Directory</a>
                        `;
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(err => {
                    console.error("AJAX Error details: ", err);
                    alert('Could not process AJAX deletion response.');
                });
            }
        };
    }
    </script>
</body>
</html>
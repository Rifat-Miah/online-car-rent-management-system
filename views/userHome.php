<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php?controller=login");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Home</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
        body { background:#f5f5f5; color:#222; }

        .topbar {
            background:#1a472a;
            color:#fff;
            padding:15px 20px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:10px;
        }

        .topbar a {
            color:#fff;
            text-decoration:none;
            margin-left:12px;
            font-size:14px;
        }

        .container {
            width:90%;
            max-width:1200px;
            margin:20px auto;
        }

        .box {
            background:#fff;
            padding:20px;
            border-radius:10px;
            margin-bottom:20px;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
        }

        .categories {
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .cat {
            padding:8px 14px;
            background:#e8f5e9;
            color:#1a472a;
            text-decoration:none;
            border-radius:20px;
            font-size:14px;
        }

        .cars {
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
            gap:15px;
        }

        .car {
            background:#fff;
            border-radius:10px;
            overflow:hidden;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
        }

        .car img {
            width:100%;
            height:160px;
            object-fit:cover;
            background:#ddd;
        }

        .car .info { padding:15px; }
        .car h3 { font-size:16px; margin-bottom:6px; }
        .car p { font-size:13px; color:#666; margin-bottom:4px; }
        .price { color:#1a472a; font-weight:bold; margin-top:8px; }

        .empty {
            text-align:center;
            padding:30px;
            color:#888;
        }
    </style>
</head>
<body>

<div class="topbar">
    <div>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></div>
    <div>
        <a href="index.php?controller=userHome">Home</a>
        <a href="index.php?controller=blog">Blog</a>
        <a href="index.php?controller=orderHistory">Order History</a>
        <a href="index.php?controller=userProfile">Profile</a>
        <a href="index.php?controller=logout">Logout</a>
    </div>
</div>

<div class="container">
    <div class="box">
        <h2>Car Categories</h2>
        <div class="categories" style="margin-top:15px;">
            <a class="cat" href="index.php?controller=userHome">All</a>
            <?php foreach ($carTypes as $type): ?>
                <a class="cat" href="index.php?controller=userHome&type=<?= urlencode($type) ?>">
                    <?= htmlspecialchars($type) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="box">
        <h2>Featured Cars</h2>
        <div style="margin-top:15px;">
            <?php if (empty($featuredCars)): ?>
                <div class="empty">No featured cars found.</div>
            <?php else: ?>
                <div class="cars">
                    <?php foreach ($featuredCars as $car): ?>
                        <div class="car">
                            <img src="<?= htmlspecialchars($car['image'] ?? 'uploads/default-car.jpg') ?>" alt="Car">
                            <div class="info">
                                <h3><?= htmlspecialchars($car['name']) ?></h3>
                                <p>Model: <?= htmlspecialchars($car['model']) ?></p>
                                <p>Type: <?= htmlspecialchars($car['type']) ?></p>
                                <p>Status: <?= htmlspecialchars($car['availability_status']) ?></p>
                                <div class="price">BDT <?= number_format($car['price_per_day']) ?>/day</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
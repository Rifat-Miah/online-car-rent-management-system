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

        .search-wrap {
            position:relative;
            margin-bottom:20px;
        }

        #liveSearch {
            width:100%;
            padding:12px 18px;
            border:2px solid #ccc;
            border-radius:25px;
            font-size:15px;
            outline:none;
            transition:border 0.2s;
        }

        #liveSearch:focus { border-color:#1a472a; }

        #searchSpinner {
            position:absolute;
            right:18px; top:50%;
            transform:translateY(-50%);
            display:none;
            width:18px; height:18px;
            border:3px solid #ccc;
            border-top-color:#1a472a;
            border-radius:50%;
            animation:spin .6s linear infinite;
        }

        @keyframes spin { to { transform:translateY(-50%) rotate(360deg); } }

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
            cursor:pointer;
            border:2px solid transparent;
            transition:0.2s;
        }

        .cat:hover, .cat.active {
            background:#1a472a;
            color:#fff;
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
            transition:transform 0.2s;
        }

        .car:hover { transform:translateY(-4px); }

        .car img {
            width:100%;
            height:160px;
            object-fit:cover;
            background:#ddd;
        }

        .car .info { padding:15px; }
        .car h3 { font-size:16px; margin-bottom:6px; }
        .car p  { font-size:13px; color:#666; margin-bottom:4px; }
        .price  { color:#1a472a; font-weight:bold; margin-top:8px; }

        .empty {
            text-align:center;
            padding:30px;
            color:#888;
        }
        .section-head {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:15px;
        }

        #carsTitle { font-size:18px; font-weight:bold; }
        #resultCount { font-size:13px; color:#888; }
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
        <div class="search-wrap">
            <input type="text" id="liveSearch" placeholder="🔍 Search cars by name, model or type..." />
            <div id="searchSpinner"></div>
        </div>
    </div>

    <div class="box">
        <h2>Car Categories</h2>
        <div class="categories" style="margin-top:15px;" id="categoryList">
            <span class="cat active" data-type="">All</span>
            <?php foreach ($carTypes as $type): ?>
                <span class="cat" data-type="<?= htmlspecialchars($type) ?>">
                    <?= htmlspecialchars($type) ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="box">
        <div class="section-head">
            <span id="carsTitle">Featured Cars</span>
            <span id="resultCount"></span>
        </div>
        <div id="carsGrid">
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

<script>
    let activeType   = '';
    let searchTimer  = null;

    const searchInput  = document.getElementById('liveSearch');
    const spinner      = document.getElementById('searchSpinner');
    const carsGrid     = document.getElementById('carsGrid');
    const carsTitle    = document.getElementById('carsTitle');
    const resultCount  = document.getElementById('resultCount');
    const categoryList = document.getElementById('categoryList');

    function renderCars(cars) {
        if (!cars || cars.length === 0) {
            carsGrid.innerHTML = '<div class="empty">No cars found.</div>';
            resultCount.textContent = '';
            return;
        }

        resultCount.textContent = cars.length + ' car(s) found';

        const html = cars.map(car => `
            <div class="car">
                <img src="${escHtml(car.image || 'uploads/default-car.jpg')}" alt="Car"
                     onerror="this.src='uploads/default-car.jpg'">
                <div class="info">
                    <h3>${escHtml(car.name)}</h3>
                    <p>Model: ${escHtml(car.model)}</p>
                    <p>Type: ${escHtml(car.type)}</p>
                    <p>Status: ${escHtml(car.availability_status)}</p>
                    <div class="price">BDT ${Number(car.price_per_day).toLocaleString()}/day</div>
                </div>
            </div>
        `).join('');

        carsGrid.innerHTML = `<div class="cars">${html}</div>`;
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;');
    }
    function fetchCars(type, search) {
        spinner.style.display = 'block';

        const params = new URLSearchParams({
            controller: 'ajaxCars',
            type:   type   || '',
            search: search || ''
        });

        fetch('index.php?' + params.toString())
            .then(r => {
                if (!r.ok) throw new Error('Network error');
                return r.json();
            })
            .then(data => {
                renderCars(data);
                spinner.style.display = 'none';
            })
            .catch(() => {
                carsGrid.innerHTML = '<div class="empty">Something went wrong. Please try again.</div>';
                spinner.style.display = 'none';
            });
    }

    categoryList.addEventListener('click', function(e) {
        const cat = e.target.closest('.cat');
        if (!cat) return;

        document.querySelectorAll('.cat').forEach(c => c.classList.remove('active'));
        cat.classList.add('active');

        activeType = cat.dataset.type || '';
        searchInput.value = '';          

        if (activeType === '') {
            carsTitle.textContent = 'Featured Cars';
        } else {
            carsTitle.textContent = activeType + ' Cars';
        }

        fetchCars(activeType, '');
    });

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        const q = this.value.trim();

        if (q.length === 0) {
            carsTitle.textContent = activeType ? activeType + ' Cars' : 'Featured Cars';
            fetchCars(activeType, '');
            return;
        }

        if (q.length < 2) return;

        spinner.style.display = 'block';
        carsTitle.textContent = 'Search Results';

        searchTimer = setTimeout(() => {
            fetchCars(activeType, q);
        }, 400);
    });
</script>

</body>
</html>
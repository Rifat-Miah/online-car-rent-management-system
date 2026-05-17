<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        * { margin:0; padding:0; font-family: Arial, sans-serif; box-sizing:border-box; }

        body {
            display:flex;
            min-height:100vh;
            background:#f0f0f0;
        }

        .sidebar {
            width:160px;
            background:linear-gradient(180deg, #1a472a, #2d6a4f);
            display:flex;
            flex-direction:column;
            align-items:center;
            padding:20px 0;
            position:fixed;
            height:100vh;
        }

        .sidebar .logo {
            font-size:17px;
            font-weight:bold;
            color:white;
            margin-bottom:20px;
            text-align:center;
            padding:0 10px;
        }

        .admin-avatar {
            width:60px;
            height:60px;
            border-radius:50%;
            background:rgba(255,255,255,0.2);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:22px;
            font-weight:bold;
            color:white;
            margin-bottom:8px;
        }

        .admin-name {
            font-size:12px;
            color:rgba(255,255,255,0.8);
            margin-bottom:25px;
            text-align:center;
            padding:0 10px;
        }

        .nav-item {
            width:100%;
            padding:12px 20px;
            color:rgba(255,255,255,0.8);
            cursor:pointer;
            font-size:14px;
            border-left:3px solid transparent;
            transition:0.2s;
            text-decoration:none;
            display:block;
        }

        .nav-item:hover,
        .nav-item.active {
            background:rgba(255,255,255,0.15);
            border-left:3px solid white;
            color:white;
        }

        .sidebar-footer {
            margin-top:auto;
            font-size:11px;
            color:rgba(255,255,255,0.4);
            text-align:center;
            padding:10px;
        }

        .main {
            margin-left:160px;
            flex:1;
            padding:30px;
        }

        .topbar {
            margin-bottom:25px;
        }

        .topbar h2 {
            font-size:22px;
            color:#222;
        }

        .topbar p {
            font-size:13px;
            color:#888;
            margin-top:3px;
        }

        .stats {
            display:flex;
            gap:20px;
            margin-bottom:30px;
            flex-wrap:wrap;
        }

        .stat-card {
            flex:1;
            min-width:150px;
            background:linear-gradient(135deg, #1a472a, #2d6a4f);
            border-radius:12px;
            padding:25px 20px;
            color:white;
            text-align:center;
        }

        .stat-card h3 { font-size:30px; font-weight:bold; }
        .stat-card p  { font-size:13px; opacity:0.85; margin-top:5px; }

        .section-box {
            background:white;
            border-radius:12px;
            padding:25px;
            box-shadow:0 2px 10px rgba(0,0,0,0.08);
        }

        .section-box h3 {
            font-size:17px;
            color:#222;
            margin-bottom:20px;
            border-bottom:2px solid #2d6a4f;
            padding-bottom:10px;
        }

        .section { display:none; }
        .section.active { display:block; }

        .form-grid {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:15px;
        }

        .form-grid input,
        .form-grid select,
        .form-grid textarea {
            width:100%;
            padding:10px 12px;
            border-radius:8px;
            border:1px solid #ddd;
            font-size:14px;
            outline:none;
            color:#333;
            transition:0.2s;
        }

        .form-grid input:focus,
        .form-grid select:focus,
        .form-grid textarea:focus {
            border-color:#2d6a4f;
        }

        .form-grid textarea {
            resize:vertical;
            min-height:80px;
        }

        .form-grid .full-width { grid-column:1 / -1; }

        .submit-btn {
            padding:11px 30px;
            background:linear-gradient(135deg, #1a472a, #2d6a4f);
            color:white;
            border:none;
            border-radius:8px;
            cursor:pointer;
            font-weight:bold;
            font-size:15px;
        }

        table {
            width:100%;
            border-collapse:collapse;
        }

        th {
            background:#f5f5f5;
            padding:12px 15px;
            text-align:left;
            font-size:13px;
            color:#555;
            border-bottom:2px solid #eee;
        }

        td {
            padding:12px 15px;
            border-bottom:1px solid #f0f0f0;
            font-size:14px;
            color:#444;
        }

        tr:hover td { background:#f9f9f9; }

        .delete-btn {
            padding:6px 14px;
            background:#c0392b;
            color:white;
            border:none;
            border-radius:6px;
            cursor:pointer;
            font-size:12px;
        }

        .badge {
            padding:4px 10px;
            border-radius:20px;
            font-size:11px;
            font-weight:bold;
        }

        .badge-admin     { background:red;    color:white; }
        .badge-member    { background:green;  color:white; }
        .badge-pending   { background:orange; color:white; }
        .badge-confirmed { background:green;  color:white; }
        .badge-cancelled { background:red;    color:white; }

        .success-msg {
            background:#e8f5e9;
            border:1px solid #2d6a4f;
            padding:10px 15px;
            border-radius:8px;
            margin-bottom:15px;
            font-size:14px;
            color:#1a472a;
        }

        .empty {
            text-align:center;
            padding:40px;
            color:#aaa;
            font-size:15px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">Car Rent Admin</div>

        <div class="admin-avatar">
            <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
        </div>
        <div class="admin-name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>

        <a class="nav-item active" onclick="showTab('home', this)">Home</a>
        <a class="nav-item" onclick="showTab('cars', this)">Post Car</a>
        <a class="nav-item" onclick="showTab('members', this)">Members</a>
        <a class="nav-item" onclick="showTab('orders', this)">Rent Orders</a>
        <a class="nav-item" onclick="showTab('blogs', this)">Blog Posts</a>
        <a class="nav-item" href="index.php?controller=adminLogout">Logout</a>

        <div class="sidebar-footer">Car Rent 2026</div>
    </div>

    <div class="main">

        <div id="home" class="section active">
            <div class="topbar">
                <h2>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?></h2>
                <p>Admin Control Panel</p>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <h3><?= $totalCars ?></h3>
                    <p>Total Cars</p>
                </div>
                <div class="stat-card">
                    <h3><?= $totalMembers ?></h3>
                    <p>Members</p>
                </div>
                <div class="stat-card">
                    <h3><?= $totalOrders ?></h3>
                    <p>Orders</p>
                </div>
                <div class="stat-card">
                    <h3><?= $totalBlogs ?></h3>
                    <p>Blog Posts</p>
                </div>
            </div>
        </div>

        <div id="cars" class="section">
            <div class="section-box">
                <h3>Post Car Availability & Cost</h3>

                <?php if (isset($_GET['car_added'])): ?>
                    <div class="success-msg">Car posted successfully!</div>
                <?php endif; ?>

                <form action="index.php?controller=adminDashboard&action=addCar" method="POST">
                    <div class="form-grid">
                        <input type="text" name="name" placeholder="Car Name (e.g. Toyota Corolla)" required />
                        <input type="text" name="model" placeholder="Model (e.g. X Corolla 2022)" required />

                        <select name="type" required>
                            <option value=""> Car Type</option>
                            <option value="Private car">Private Car</option>
                            <option value="Microbus">Microbus</option>
                            <option value="PickUp">PickUp</option>
                            <option value="SUV">SUV</option>
                            <option value="Van">Van</option>
                            <option value="Sedan">Sedan</option>
                        </select>

                        <input type="number" name="price_per_day" placeholder="Price per Day (BDT)" required />

                        <select name="availability_status" required>
                            <option value="">Availability</option>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>

                        <textarea name="description" class="full-width" placeholder="Car Description"></textarea>

                        <div class="full-width">
                            <button type="submit" class="submit-btn">Post Car</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="members" class="section">
            <div class="section-box">
                <h3>All Members</h3>

                <?php if (isset($_GET['member_deleted'])): ?>
                    <div class="success-msg">Member deleted successfully!</div>
                <?php endif; ?>

                <?php if (empty($members)): ?>
                    <p class="empty">No members found.</p>
                <?php else: ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?= $member['id'] ?></td>
                        <td><?= htmlspecialchars($member['name']) ?></td>
                        <td><?= htmlspecialchars($member['email']) ?></td>
                        <td><?= htmlspecialchars($member['phone']) ?></td>
                        <td>
                            <span class="badge badge-<?= $member['role'] ?>">
                                <?= strtoupper($member['role']) ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($member['created_at'])) ?></td>
                        <td>
                            <form action="index.php?controller=adminDashboard&action=deleteMember"
                                  method="POST" onsubmit="return confirm('Delete this member?')">
                                <input type="hidden" name="member_id" value="<?= $member['id'] ?>" />
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <div id="orders" class="section">
            <div class="section-box">
                <h3>All Rent Orders</h3>

                <?php if (empty($orders)): ?>
                    <p class="empty">No orders found.</p>
                <?php else: ?>
                <table>
                    <tr>
                        <th>Order ID</th>
                        <th>Member</th>
                        <th>Car</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Total (BDT)</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['user_name']) ?></td>
                        <td><?= htmlspecialchars($order['car_name'] . ' ' . $order['model']) ?></td>
                        <td><?= $order['start_date'] ?></td>
                        <td><?= $order['end_date'] ?></td>
                        <td><?= number_format($order['total_price']) ?></td>
                        <td>
                            <span class="badge badge-<?= $order['status'] ?>">
                                <?= strtoupper($order['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <div id="blogs" class="section">
            <div class="section-box">
                <h3>All Blog Posts</h3>

                <?php if (isset($_GET['blog_deleted'])): ?>
                    <div class="success-msg">Blog post deleted successfully!</div>
                <?php endif; ?>

                <?php if (empty($blogs)): ?>
                    <p class="empty">No blog posts found.</p>
                <?php else: ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Posted</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($blogs as $blog): ?>
                    <tr>
                        <td>#<?= $blog['id'] ?></td>
                        <td><?= htmlspecialchars($blog['title']) ?></td>
                        <td><?= htmlspecialchars($blog['author_name']) ?></td>
                        <td><?= date('d M Y', strtotime($blog['created_at'])) ?></td>
                        <td>
                            <form action="index.php?controller=adminDashboard&action=deleteBlog"
                                  method="POST" onsubmit="return confirm('Delete this blog post?')">
                                <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>" />
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script>
        function showTab(tabName, el) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            if (el) el.classList.add('active');
        }
    </script>

</body>
</html>
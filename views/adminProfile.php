<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <style>
        * { margin:0; padding:0; font-family:Arial, sans-serif; box-sizing:border-box; }

        body { display:flex; min-height:100vh; background:#f0f0f0; }

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
            font-size:17px; font-weight:bold; color:white;
            margin-bottom:20px; text-align:center; padding:0 10px;
        }

        .admin-avatar {
            width:60px; height:60px; border-radius:50%;
            background:rgba(255,255,255,0.2);
            display:flex; align-items:center; justify-content:center;
            font-size:22px; font-weight:bold; color:white; margin-bottom:8px;
            overflow:hidden;
        }

        .admin-avatar img { width:100%; height:100%; object-fit:cover; }

        .admin-name {
            font-size:12px; color:rgba(255,255,255,0.8);
            margin-bottom:25px; text-align:center; padding:0 10px;
        }

        .nav-item {
            width:100%; padding:12px 20px;
            color:rgba(255,255,255,0.8); cursor:pointer;
            font-size:14px; border-left:3px solid transparent;
            transition:0.2s; text-decoration:none; display:block;
        }

        .nav-item:hover, .nav-item.active {
            background:rgba(255,255,255,0.15);
            border-left:3px solid white; color:white;
        }

        .sidebar-footer {
            margin-top:auto; font-size:11px;
            color:rgba(255,255,255,0.4); text-align:center; padding:10px;
        }

        .main { margin-left:160px; flex:1; padding:30px; }

        .page-title {
            font-size:22px; color:#222;
            margin-bottom:25px;
            border-bottom:2px solid #2d6a4f;
            padding-bottom:10px;
        }

        .profile-grid {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:25px;
        }

        .box {
            background:white;
            border-radius:12px;
            padding:25px;
            box-shadow:0 2px 10px rgba(0,0,0,0.08);
        }

        .box h3 {
            font-size:16px; color:#222;
            margin-bottom:20px;
            border-bottom:2px solid #2d6a4f;
            padding-bottom:8px;
        }

        .profile-pic-box {
            text-align:center;
            margin-bottom:20px;
        }

        .profile-pic-box img,
        .profile-pic-box .avatar-big {
            width:100px; height:100px; border-radius:50%;
            object-fit:cover;
            border:3px solid #2d6a4f;
            margin-bottom:10px;
        }

        .avatar-big {
            background:#2d6a4f; color:white;
            display:flex; align-items:center; justify-content:center;
            font-size:36px; font-weight:bold;
            margin:0 auto 10px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width:100%; padding:10px 12px;
            border-radius:8px; border:1px solid #ddd;
            font-size:14px; outline:none; color:#333;
            margin-bottom:12px; transition:0.2s;
        }

        input:focus { border-color:#2d6a4f; }

        .submit-btn {
            padding:10px 25px;
            background:linear-gradient(135deg, #1a472a, #2d6a4f);
            color:white; border:none; border-radius:8px;
            cursor:pointer; font-weight:bold; font-size:14px;
            width:100%;
        }

        .success-msg {
            background:#e8f5e9; border:1px solid #2d6a4f;
            padding:12px 15px; border-radius:8px;
            margin-bottom:20px; font-size:14px; color:#1a472a;
        }

        .error-msg {
            background:#fde8e8; border:1px solid #c0392b;
            padding:12px 15px; border-radius:8px;
            margin-bottom:20px; font-size:14px; color:#c0392b;
        }

        label { font-size:13px; color:#555; margin-bottom:4px; display:block; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">Car Rent Admin</div>
        <div class="admin-avatar">
            <?php if (!empty($user['profile_picture'])): ?>
                <img src="uploads/<?= $user['profile_picture'] ?>" />
            <?php else: ?>
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div class="admin-name"><?= htmlspecialchars($user['name']) ?></div>

        <a class="nav-item" href="index.php?controller=adminDashboard">Home</a>
        <a class="nav-item active" href="index.php?controller=adminProfile">Profile</a>
        <a class="nav-item" href="index.php?controller=adminLogout">Logout</a>

        <div class="sidebar-footer">Car Rent 2026</div>
    </div>

    <div class="main">
        <div class="page-title">My Profile</div>

        <?php if (!empty($success)): ?>
            <div class="success-msg"><?= $success ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <div class="profile-grid">

            <div class="box">
                <h3>Update Profile</h3>

                <div class="profile-pic-box">
                    <?php if (!empty($user['profile_picture'])): ?>
                        <img src="uploads/<?= $user['profile_picture'] ?>" />
                    <?php else: ?>
                        <div class="avatar-big">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <form action="index.php?controller=adminProfile" method="POST" enctype="multipart/form-data">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required />

                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required />

                    <label>Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" />

                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" />

                    <label>Profile Picture</label>
                    <input type="file" name="profile_picture" accept="image/*" />

                    <button type="submit" name="update_profile" class="submit-btn">Save Changes</button>
                </form>
            </div>

            <div class="box">
                <h3>Change Password</h3>

                <form action="index.php?controller=adminProfile" method="POST">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="Current Password" required />

                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="New Password (min 8)" required />

                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required />

                    <button type="submit" name="change_password" class="submit-btn">Change Password</button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>
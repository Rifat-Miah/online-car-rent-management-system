<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <style>
        * { margin:0; padding:0; font-family:Arial, sans-serif; box-sizing:border-box; }

        body {
            min-height:100vh;
            background:url('homepazeImg.png') no-repeat center center/cover;
            position:relative;
        }

        body::before {
            content:"";
            position:absolute;
            width:100%; height:100%;
            background:rgba(49,49,49,0.7);
        }

        .navbar {
            position:relative; z-index:1;
            background:rgba(0,0,0,0.5);
            padding:15px 30px;
            display:flex; justify-content:space-between; align-items:center;
            border-bottom:2px solid green;
        }

        .navbar h2 { color:white; font-size:18px; }

        .navbar .nav-links { display:flex; gap:15px; align-items:center; }

        .navbar a {
            color:#ddd; text-decoration:none;
            font-size:14px; padding:7px 14px;
            border-radius:5px; transition:0.2s;
        }

        .navbar a:hover, .navbar a.active {
            background:green; color:white;
        }

        .navbar .logout {
            background:rgba(255,0,0,0.6); color:white;
        }

        .main {
            position:relative; z-index:1;
            max-width:900px; margin:40px auto; padding:0 20px;
        }

        .page-title {
            font-size:22px; color:white;
            margin-bottom:25px;
        }

        .profile-grid {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:25px;
        }

        .box {
            background:rgba(255,255,255,0.1);
            backdrop-filter:blur(6px);
            border-radius:12px; padding:25px; color:white;
        }

        .box h3 {
            font-size:16px; margin-bottom:20px;
            border-bottom:2px solid green; padding-bottom:8px;
        }

        .profile-pic-box { text-align:center; margin-bottom:20px; }

        .profile-pic-box img,
        .avatar-big {
            width:100px; height:100px; border-radius:50%;
            object-fit:cover; border:3px solid green;
            margin-bottom:10px;
        }

        .avatar-big {
            background:green; color:white;
            display:flex; align-items:center; justify-content:center;
            font-size:36px; font-weight:bold; margin:0 auto 10px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width:100%; padding:10px 12px;
            border-radius:8px; border:none;
            background:rgba(255,255,255,0.15);
            color:white; font-size:14px;
            outline:none; margin-bottom:12px;
        }

        input::placeholder { color:#ccc; }

        .submit-btn {
            width:100%; padding:10px 25px;
            background:green; color:white; border:none;
            border-radius:8px; cursor:pointer;
            font-weight:bold; font-size:14px;
        }

        .success-msg {
            background:rgba(0,255,0,0.15); border:1px solid green;
            padding:12px 15px; border-radius:8px;
            margin-bottom:20px; font-size:14px; color:#6bff6b;
        }

        .error-msg {
            background:rgba(255,0,0,0.2); border:1px solid red;
            padding:12px 15px; border-radius:8px;
            margin-bottom:20px; font-size:14px; color:#ff6b6b;
        }

        label { font-size:13px; color:#ddd; margin-bottom:4px; display:block; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Car Rent</h2>
        <div class="nav-links">
            <a href="index.php?controller=userDashboard">Dashboard</a>
            <a href="index.php?controller=userProfile" class="active">Profile</a>
            <a href="index.php?controller=userLogout" class="logout">Logout</a>
        </div>
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

                <form action="index.php?controller=userProfile" method="POST" enctype="multipart/form-data">
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

                <form action="index.php?controller=userProfile" method="POST">
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
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <style>
        * { margin:0; padding:0; font-family: Arial, sans-serif; }

        body {
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:url('homepazeImg.png') no-repeat center center/cover;
        }
        body::before {
            content:"";
            position:absolute;
            width:100%;
            height:100%;
            background:rgba(49,49,49,0.6);
        }

        .container {
            position:relative;
            z-index:1;
            width:380px;
            padding:40px;
            background:rgba(255,255,255,0.1);
            backdrop-filter:blur(6px);
            border-radius:12px;
            color:white;
            text-align:center;
        }

        h1 { margin-bottom:20px; font-size:28px; }

        input, select {
            width:100%;
            padding:10px;
            margin:8px 0;
            border-radius:6px;
            border:none;
            outline:none;
            font-size:14px;
            box-sizing:border-box;
        }

        select { color:#333; }

        .btn {
            width:100%;
            padding:12px;
            margin-top:15px;
            border:none;
            border-radius:6px;
            background:blue;
            color:white;
            font-weight:bold;
            font-size:15px;
            cursor:pointer;
        }

        .error {
            background:rgba(255,0,0,0.3);
            padding:10px;
            border-radius:6px;
            margin-bottom:10px;
            font-size:13px;
        }

        .login-link {
            margin-top:15px;
            font-size:13px;
            color:#ddd;
        }

        .login-link a { color:lightblue; text-decoration:none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sign Up</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form action="index.php?controller=signup" method="POST">
            <input type="text" name="name" placeholder="Full Name" value="<?= $old['name'] ?? '' ?>" required />
            <input type="email" name="email" placeholder="Email" value="<?= $old['email'] ?? '' ?>" required />
            <input type="password" name="password" placeholder="Password (min 8 chars)" required />
            <input type="password" name="confirm_password" placeholder="Confirm Password" required />
            <input type="text" name="address" placeholder="Address" value="<?= $old['address'] ?? '' ?>" required />
            <input type="text" name="phone" placeholder="Phone Number" value="<?= $old['phone'] ?? '' ?>" required />

            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="admin" <?= (isset($old['role']) && $old['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                <option value="member" <?= (isset($old['role']) && $old['role'] === 'member') ? 'selected' : '' ?>>Member</option>
            </select>

            <button type="submit" class="btn">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="index.php?controllers=login">Login</a>
        </div>
    </div>
</body>
</html>
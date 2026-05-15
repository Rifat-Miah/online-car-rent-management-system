<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
            width:350px;
            padding:40px;
            background:rgba(255,255,255,0.1);
            backdrop-filter:blur(6px);
            border-radius:12px;
            color:white;
            text-align:center;
        }

        h1 { margin-bottom:20px; font-size:28px; }

        input {
            width:100%;
            padding:10px;
            margin:8px 0;
            border-radius:6px;
            border:none;
            outline:none;
            font-size:14px;
            box-sizing:border-box;
        }

        .btn {
            width:100%;
            padding:12px;
            margin-top:15px;
            border:none;
            border-radius:6px;
            background:green;
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

        .success {
            background:rgba(0,255,0,0.2);
            padding:10px;
            border-radius:6px;
            margin-bottom:10px;
            font-size:13px;
        }

        .links {
            margin-top:15px;
            font-size:13px;
            color:#ddd;
        }

        .links a { color:lightblue; text-decoration:none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="success">Registration successful! Please login.</div>
        <?php endif; ?>

        <form action="index.php?controller=login" method="POST">
            <input type="email" name="email" placeholder="Email" value="<?= $old['email'] ?? '' ?>" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit" class="btn">Login</button>
        </form>

        <div class="links">
            Don't have an account? <a href="index.php?controller=signup">Sign Up</a>
        </div>
    </div>
</body>
</html>
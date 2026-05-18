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
        .navbar a:hover, .navbar a.active { background:green; color:white; }
        .navbar .logout { background:rgba(255,0,0,0.6); color:white; }

        .main {
            position:relative; z-index:1;
            max-width:900px; margin:40px auto; padding:0 20px;
        }

        .page-title { font-size:22px; color:white; margin-bottom:25px; }

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
            border-radius:8px; border:2px solid transparent;
            background:rgba(255,255,255,0.15);
            color:white; font-size:14px;
            outline:none; margin-bottom:2px;
            transition: border 0.2s;
        }

        input.invalid { border:2px solid #ff6b6b !important; }
        input.valid   { border:2px solid #6bff6b !important; }
        input::placeholder { color:#ccc; }

        .field-error {
            color:#ff9999;
            font-size:12px;
            display:block;
            min-height:16px;
            margin-bottom:8px;
        }

        .submit-btn {
            width:100%; padding:10px 25px;
            background:green; color:white; border:none;
            border-radius:8px; cursor:pointer;
            font-weight:bold; font-size:14px;
            margin-top:6px;
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

                <form id="profileForm" action="index.php?controller=userProfile"
                      method="POST" enctype="multipart/form-data" novalidate>

                    <label>Full Name</label>
                    <input type="text" id="pName" name="name"
                           value="<?= htmlspecialchars($user['name']) ?>" />
                    <span class="field-error" id="pNameErr"></span>

                    <label>Email</label>
                    <input type="email" id="pEmail" name="email"
                           value="<?= htmlspecialchars($user['email']) ?>" />
                    <span class="field-error" id="pEmailErr"></span>

                    <label>Address</label>
                    <input type="text" id="pAddress" name="address"
                           value="<?= htmlspecialchars($user['address']) ?>" />
                    <span class="field-error" id="pAddressErr"></span>

                    <label>Phone</label>
                    <input type="text" id="pPhone" name="phone"
                           value="<?= htmlspecialchars($user['phone']) ?>" />
                    <span class="field-error" id="pPhoneErr"></span>

                    <label>Profile Picture</label>
                    <input type="file" id="pPic" name="profile_picture" accept="image/*" />
                    <span class="field-error" id="pPicErr"></span>

                    <button type="submit" name="update_profile" class="submit-btn">Save Changes</button>
                </form>
            </div>

            <div class="box">
                <h3>Change Password</h3>

                <form id="passwordForm" action="index.php?controller=userProfile"
                      method="POST" novalidate>

                    <label>Current Password</label>
                    <input type="password" id="curPass" name="current_password"
                           placeholder="Current Password" />
                    <span class="field-error" id="curPassErr"></span>

                    <label>New Password</label>
                    <input type="password" id="newPass" name="new_password"
                           placeholder="New Password (min 8)" />
                    <span class="field-error" id="newPassErr"></span>

                    <label>Confirm New Password</label>
                    <input type="password" id="conPass" name="confirm_password"
                           placeholder="Confirm New Password" />
                    <span class="field-error" id="conPassErr"></span>

                    <button type="submit" name="change_password" class="submit-btn">Change Password</button>
                </form>
            </div>

        </div>
    </div>

    <script>
        function showErr(id, msg) { document.getElementById(id).textContent = msg; }
        function clearErr(id)     { showErr(id, ''); }
        function mark(el, valid) {
            el.classList.toggle('valid',   valid);
            el.classList.toggle('invalid', !valid);
        }

        function valPName() {
            const el = document.getElementById('pName');
            const v  = el.value.trim();
            if (!v)         { showErr('pNameErr', 'Name is required.');              mark(el, false); return false; }
            if (v.length<3) { showErr('pNameErr', 'Name must be at least 3 chars.'); mark(el, false); return false; }
            clearErr('pNameErr'); mark(el, true); return true;
        }

        function valPEmail() {
            const el = document.getElementById('pEmail');
            const v  = el.value.trim();
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!v)         { showErr('pEmailErr', 'Email is required.');       mark(el, false); return false; }
            if (!re.test(v)){ showErr('pEmailErr', 'Enter a valid email.');     mark(el, false); return false; }
            clearErr('pEmailErr'); mark(el, true); return true;
        }

        function valPPhone() {
            const el = document.getElementById('pPhone');
            const v  = el.value.trim();
            const re = /^[0-9]{11}$/;
            if (v && !re.test(v)) { showErr('pPhoneErr', 'Phone must be exactly 11 digits.'); mark(el, false); return false; }
            clearErr('pPhoneErr'); if(v) mark(el, true); return true;
        }

        function valPPic() {
            const el      = document.getElementById('pPic');
            const allowed = ['jpg','jpeg','png','gif'];
            if (el.files.length > 0) {
                const ext = el.files[0].name.split('.').pop().toLowerCase();
                if (!allowed.includes(ext)) {
                    showErr('pPicErr', 'Only JPG, PNG, GIF allowed.'); return false;
                }
                const maxMB = 2;
                if (el.files[0].size > maxMB * 1024 * 1024) {
                    showErr('pPicErr', `File must be under ${maxMB}MB.`); return false;
                }
            }
            clearErr('pPicErr'); return true;
        }
        document.getElementById('pName').addEventListener('blur',   valPName);
        document.getElementById('pEmail').addEventListener('blur',  valPEmail);
        document.getElementById('pPhone').addEventListener('blur',  valPPhone);
        document.getElementById('pPic').addEventListener('change',  valPPic);

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const ok = [valPName(), valPEmail(), valPPhone(), valPPic()].every(Boolean);
            if (!ok) {
                e.preventDefault();
                const first = this.querySelector('.invalid');
                if (first) first.scrollIntoView({ behavior:'smooth', block:'center' });
            }
        });
        function valCurPass() {
            const el = document.getElementById('curPass');
            if (!el.value) { showErr('curPassErr', 'Current password is required.'); mark(el, false); return false; }
            clearErr('curPassErr'); mark(el, true); return true;
        }

        function valNewPass() {
            const el = document.getElementById('newPass');
            const v  = el.value;
            if (!v)          { showErr('newPassErr', 'New password is required.');              mark(el, false); return false; }
            if (v.length < 8){ showErr('newPassErr', 'Password must be at least 8 characters.'); mark(el, false); return false; }
            clearErr('newPassErr'); mark(el, true);
            valConPass();
            return true;
        }

        function valConPass() {
            const np = document.getElementById('newPass').value;
            const el = document.getElementById('conPass');
            if (!el.value)   { showErr('conPassErr', 'Please confirm new password.'); mark(el, false); return false; }
            if (np !== el.value) { showErr('conPassErr', 'Passwords do not match.');  mark(el, false); return false; }
            clearErr('conPassErr'); mark(el, true); return true;
        }

        document.getElementById('curPass').addEventListener('blur', valCurPass);
        document.getElementById('newPass').addEventListener('blur', valNewPass);
        document.getElementById('conPass').addEventListener('blur', valConPass);

        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const ok = [valCurPass(), valNewPass(), valConPass()].every(Boolean);
            if (!ok) {
                e.preventDefault();
                const first = this.querySelector('.invalid');
                if (first) first.scrollIntoView({ behavior:'smooth', block:'center' });
            }
        });
    </script>

</body>
</html>
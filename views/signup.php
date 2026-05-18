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
            margin:8px 0 2px 0;
            border-radius:6px;
            border:2px solid transparent;
            outline:none;
            font-size:14px;
            box-sizing:border-box;
            transition: border 0.2s;
        }

        input.invalid { border:2px solid #ff6b6b; }
        input.valid   { border:2px solid #6bff6b; }

        select { color:#333; margin:8px 0 2px 0; }

        .field-error {
            color:#ff9999;
            font-size:12px;
            text-align:left;
            margin-bottom:4px;
            min-height:16px;
            display:block;
        }

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

        .btn:disabled {
            background:#555;
            cursor:not-allowed;
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

        <form id="signupForm" action="index.php?controller=signup" method="POST" novalidate>

            <input type="text" id="name" name="name" placeholder="Full Name"
                   value="<?= $old['name'] ?? '' ?>" />
            <span class="field-error" id="nameErr"></span>

            <input type="email" id="email" name="email" placeholder="Email"
                   value="<?= $old['email'] ?? '' ?>" />
            <span class="field-error" id="emailErr"></span>

            <input type="password" id="password" name="password" placeholder="Password (min 8 chars)" />
            <span class="field-error" id="passErr"></span>

            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" />
            <span class="field-error" id="confirmErr"></span>

            <input type="text" id="address" name="address" placeholder="Address"
                   value="<?= $old['address'] ?? '' ?>" />
            <span class="field-error" id="addressErr"></span>

            <input type="text" id="phone" name="phone" placeholder="Phone Number (11 digits)"
                   value="<?= $old['phone'] ?? '' ?>" />
            <span class="field-error" id="phoneErr"></span>

            <select id="role" name="role">
                <option value="">-- Select Role --</option>
                <option value="admin"  <?= (isset($old['role']) && $old['role'] === 'admin')  ? 'selected' : '' ?>>Admin</option>
                <option value="member" <?= (isset($old['role']) && $old['role'] === 'member') ? 'selected' : '' ?>>Member</option>
            </select>
            <span class="field-error" id="roleErr"></span>

            <button type="submit" class="btn" id="submitBtn">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="index.php?controller=login">Login</a>
        </div>
    </div>

    <script>
        function showErr(id, msg) {
            const el = document.getElementById(id);
            el.textContent = msg;
        }
        function clearErr(id) { showErr(id, ''); }

        function markField(input, valid) {
            input.classList.toggle('valid',   valid);
            input.classList.toggle('invalid', !valid);
        }
        function validateName() {
            const v = document.getElementById('name').value.trim();
            if (!v)              { showErr('nameErr', 'Full name is required.'); markField(document.getElementById('name'), false); return false; }
            if (v.length < 3)    { showErr('nameErr', 'Name must be at least 3 characters.'); markField(document.getElementById('name'), false); return false; }
            clearErr('nameErr'); markField(document.getElementById('name'), true); return true;
        }

        function validateEmail() {
            const v = document.getElementById('email').value.trim();
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!v)        { showErr('emailErr', 'Email is required.'); markField(document.getElementById('email'), false); return false; }
            if (!re.test(v)) { showErr('emailErr', 'Enter a valid email address.'); markField(document.getElementById('email'), false); return false; }
            clearErr('emailErr'); markField(document.getElementById('email'), true); return true;
        }

        function validatePassword() {
            const v = document.getElementById('password').value;
            if (!v)          { showErr('passErr', 'Password is required.'); markField(document.getElementById('password'), false); return false; }
            if (v.length < 8){ showErr('passErr', 'Password must be at least 8 characters.'); markField(document.getElementById('password'), false); return false; }
            clearErr('passErr'); markField(document.getElementById('password'), true);
            validateConfirm();
            return true;
        }

        function validateConfirm() {
            const p = document.getElementById('password').value;
            const c = document.getElementById('confirm_password').value;
            if (!c)      { showErr('confirmErr', 'Please confirm your password.'); markField(document.getElementById('confirm_password'), false); return false; }
            if (p !== c) { showErr('confirmErr', 'Passwords do not match.'); markField(document.getElementById('confirm_password'), false); return false; }
            clearErr('confirmErr'); markField(document.getElementById('confirm_password'), true); return true;
        }

        function validateAddress() {
            const v = document.getElementById('address').value.trim();
            if (!v) { showErr('addressErr', 'Address is required.'); markField(document.getElementById('address'), false); return false; }
            clearErr('addressErr'); markField(document.getElementById('address'), true); return true;
        }

        function validatePhone() {
            const v = document.getElementById('phone').value.trim();
            const re = /^[0-9]{11}$/;
            if (!v)         { showErr('phoneErr', 'Phone number is required.'); markField(document.getElementById('phone'), false); return false; }
            if (!re.test(v)){ showErr('phoneErr', 'Phone must be exactly 11 digits.'); markField(document.getElementById('phone'), false); return false; }
            clearErr('phoneErr'); markField(document.getElementById('phone'), true); return true;
        }

        function validateRole() {
            const v = document.getElementById('role').value;
            if (!v) { showErr('roleErr', 'Please select a role.'); return false; }
            clearErr('roleErr'); return true;
        }

        document.getElementById('name').addEventListener('blur', validateName);
        document.getElementById('email').addEventListener('blur', validateEmail);
        document.getElementById('password').addEventListener('blur', validatePassword);
        document.getElementById('confirm_password').addEventListener('blur', validateConfirm);
        document.getElementById('address').addEventListener('blur', validateAddress);
        document.getElementById('phone').addEventListener('blur', validatePhone);
        document.getElementById('role').addEventListener('change', validateRole);
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const ok = [
                validateName(),
                validateEmail(),
                validatePassword(),
                validateConfirm(),
                validateAddress(),
                validatePhone(),
                validateRole()
            ].every(Boolean);

            if (!ok) {
                e.preventDefault();
                const firstInvalid = document.querySelector('.invalid');
                if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>
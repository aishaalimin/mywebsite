<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $username   = trim($_POST['username']);
    $password   = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $address1   = trim($_POST['address1']);
    $address2   = trim($_POST['address2']);
    $city       = trim($_POST['city']);
    $state_id   = $_POST['state'];
    $gender     = isset($_POST['gender']) ? $_POST['gender'] : '';

    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");

    if (mysqli_num_rows($check) > 0) {
        $error = "Username already exists!";
    } elseif ($password != $repassword) {
        $error = "Passwords do not match!";
    } elseif (empty($first_name) || empty($last_name) || empty($username) || empty($password) || empty($email)) {
        $error = "Please fill in all required fields!";
    } else {
        $hashed = MD5($password);
        mysqli_query($conn, "INSERT INTO users (first_name, last_name, username, password, email, phone, address1, address2, city, state_id, gender)
            VALUES ('$first_name', '$last_name', '$username', '$hashed', '$email', '$phone', '$address1', '$address2', '$city', '$state_id', '$gender')");
        $success = "Registration successful! You can now login.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - My Personal Website</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #F5FFFA; color: #003333; }

        header {
            display: flex; justify-content: space-between; align-items: center;
            background: #00494C; padding: 15px 30px;
        }
        header img#logo { height: 50px; cursor: pointer; border: 2px solid #F5FFFA; border-radius: 8px; }
        .social-media a img { height: 38px; margin-left: 10px; border-radius: 6px; }
        .social-media a img:hover { opacity: 0.75; }

        nav { background: #008080; text-align: center; padding: 12px; }
        nav a { color: #F5FFFA; text-decoration: none; margin: 0 14px; font-weight: bold; font-size: 15px; }
        nav a:hover { color: #00CED1; text-decoration: underline; }

        .page-title {
            background: #008080; color: #F5FFFA;
            text-align: center; padding: 20px; font-size: 1.6em; letter-spacing: 1px;
        }

        .signup-wrapper {
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }

        .signup-box {
            background: white;
            border: 2px solid #008080;
            border-radius: 14px;
            padding: 40px;
            width: 100%;
            max-width: 680px;
            box-shadow: 0 6px 24px rgba(0,128,128,0.15);
        }

        .signup-box h2 {
            text-align: center;
            color: #00494C;
            font-size: 1.5em;
            margin-bottom: 8px;
        }

        .signup-box p.subtitle {
            text-align: center;
            color: #004D4D;
            font-size: 13px;
            margin-bottom: 30px;
        }

        .section-label td {
            background: #00494C;
            color: #F5FFFA;
            font-weight: bold;
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }

        .signup-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signup-table td {
            padding: 9px 6px;
            vertical-align: middle;
            font-size: 14px;
            color: #003333;
        }
        .signup-table td.label {
            font-weight: bold;
            color: #008080;
            width: 170px;
            white-space: nowrap;
            vertical-align: top;
            padding-top: 14px;
        }

        .signup-table input[type="text"],
        .signup-table input[type="email"],
        .signup-table input[type="password"],
        .signup-table input[type="tel"],
        .signup-table select {
            width: 100%;
            padding: 9px 14px;
            border: 2px solid #80C4C0;
            border-radius: 8px;
            font-size: 14px;
            color: #003333;
            background: #F5FFFA;
            outline: none;
            transition: border 0.2s;
        }
        .signup-table input:focus,
        .signup-table select:focus { border-color: #008080; }

        .input-error { border-color: #e53e3e !important; background: #fff5f5 !important; }

        .error-msg {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 4px;
            display: none;
            padding-left: 2px;
        }
        .error-msg.show { display: block; }

        .php-error {
            background: #fff5f5;
            border: 2px solid #e53e3e;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            color: #e53e3e;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .php-success {
            background: #E8F8F5;
            border: 2px solid #008080;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            color: #00494C;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 16px;
        }

        .signup-table input[type="radio"],
        .signup-table input[type="checkbox"] {
            margin-right: 6px;
            accent-color: #008080;
        }
        .radio-group label,
        .check-group label {
            margin-right: 16px;
            font-size: 14px;
            color: #003333;
            cursor: pointer;
        }

        .divider td { padding: 8px 0; }

        .btn-row td { text-align: center; padding-top: 20px; }

        .btn-submit {
            background: #008080; color: #F5FFFA; border: none;
            padding: 11px 30px; border-radius: 8px; font-size: 14px;
            font-weight: bold; cursor: pointer; margin: 0 5px; transition: background 0.2s;
        }
        .btn-submit:hover { background: #00494C; }

        .btn-clear {
            background: #E8F8F5; color: #008080; border: 2px solid #008080;
            padding: 11px 30px; border-radius: 8px; font-size: 14px;
            font-weight: bold; cursor: pointer; margin: 0 5px; transition: background 0.2s;
        }
        .btn-clear:hover { background: #80C4C0; color: white; }

        .btn-cancel {
            background: #f0f0f0; color: #555; border: 2px solid #ccc;
            padding: 11px 30px; border-radius: 8px; font-size: 14px;
            font-weight: bold; cursor: pointer; margin: 0 5px; transition: background 0.2s;
        }
        .btn-cancel:hover { background: #ddd; }

        .login-link {
            text-align: center; margin-top: 18px;
            font-size: 13px; color: #004D4D;
        }
        .login-link a { color: #008080; font-weight: bold; text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }

        footer { background: #00494C; color: #F5FFFA; text-align: center; padding: 18px; margin-top: 40px; font-size: 14px; }
        footer a { color: #00CED1; text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <a href="index.html"><img id="logo" src="pic/logo.jpg" alt="My Logo"></a>
    <div class="social-media">
        <a href="https://x.com/" target="_blank"><img src="pic/x.png" alt="X"></a>
        <a href="https://www.facebook.com/" target="_blank"><img src="pic/facebook.png" alt="Facebook"></a>
        <a href="https://instagram.com" target="_blank"><img src="pic/ig.jpg" alt="Instagram"></a>
        <a href="https://www.youtube.com/" target="_blank"><img src="pic/youtube.png" alt="Youtube"></a>
    </div>
</header>

<!-- NAV -->
<nav>
    <a href="biography.html">Biography</a>
    <a href="resume.html">Resume</a>
    <a href="timetable.html">Timetable</a>
    <a href="gallery.html">Galleries</a>
    <a href="download.html">Download</a>
    <a href="links.html">Links</a>
    <a href="login.php">Login</a>
    <a href="admin/logout.php">Logout</a>
</nav>

<!-- PAGE TITLE -->
<div class="page-title">Sign Up</div>

<div class="signup-wrapper">
    <div class="signup-box">

        <h2>&#128100; Create Account</h2>
        <p class="subtitle">Please fill in all the details below to register</p>

        <!-- PHP ERROR / SUCCESS -->
        <?php if ($error != ''): ?>
        <div class="php-error">&#9888; <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success != ''): ?>
        <div class="php-success">&#10003; <?php echo $success; ?>
            <br><a href="login.php" style="color:#008080;">Click here to login</a>
        </div>
        <?php endif; ?>

        <form id="signupForm" method="POST" action="" onsubmit="return validateSignup()">
        <table class="signup-table">

            <!-- PERSONAL INFO -->
            <tr class="section-label"><td colspan="2">&#128100; Personal Information</td></tr>
            <tr class="divider"><td colspan="2"></td></tr>

            <tr>
                <td class="label">First Name</td>
                <td>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter your first name">
                    <div class="error-msg" id="err_first">&#9888; First name is required.</div>
                </td>
            </tr>

            <tr>
                <td class="label">Last Name</td>
                <td>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter your last name">
                    <div class="error-msg" id="err_last">&#9888; Last name is required.</div>
                </td>
            </tr>

            <tr>
                <td class="label">Gender</td>
                <td>
                    <div class="radio-group">
                        <label><input type="radio" name="gender" value="male"> Male</label>
                        <label><input type="radio" name="gender" value="female"> Female</label>
                    </div>
                    <div class="error-msg" id="err_gender">&#9888; Please select your gender.</div>
                </td>
            </tr>

            <tr>
                <td class="label">Phone Number</td>
                <td>
                    <input type="tel" id="phone" name="phone" placeholder="e.g. 01X-XXXXXXX">
                    <div class="error-msg" id="err_phone">&#9888; Phone number is required.</div>
                </td>
            </tr>

            <tr>
                <td class="label">Email Address</td>
                <td>
                    <input type="email" id="email" name="email" placeholder="e.g. example@email.com">
                    <div class="error-msg" id="err_email">&#9888; Please enter a valid email address.</div>
                </td>
            </tr>

            <tr class="divider"><td colspan="2"></td></tr>

            <!-- ADDRESS -->
            <tr class="section-label"><td colspan="2">&#127968; Address</td></tr>
            <tr class="divider"><td colspan="2"></td></tr>

            <tr>
                <td class="label">Address 1</td>
                <td>
                    <input type="text" id="address1" name="address1" placeholder="Street address, P.O. box">
                    <div class="error-msg" id="err_address1">&#9888; Address is required.</div>
                </td>
            </tr>

            <tr>
                <td class="label">Address 2</td>
                <td>
                    <input type="text" id="address2" name="address2" placeholder="Apartment, suite, unit (optional)">
                </td>
            </tr>

            <tr>
                <td class="label">City</td>
                <td>
                    <input type="text" id="city" name="city" placeholder="Enter your city">
                    <div class="error-msg" id="err_city">&#9888; City is required.</div>
                </td>
            </tr>

            <tr>
                <td class="label">State</td>
                <td>
                    <select id="state" name="state">
                        <option value="">-- Select State --</option>
                        <?php
                        $states = mysqli_query($conn, "SELECT * FROM state ORDER BY state_name ASC");
                        while ($row = mysqli_fetch_assoc($states)) {
                            echo "<option value='" . $row['id'] . "'>" . $row['state_name'] . "</option>";
                        }
                        ?>
                    </select>
                    <div class="error-msg" id="err_state">&#9888; Please select your state.</div>
                </td>
            </tr>

            <tr class="divider"><td colspan="2"></td></tr>

            <!-- EDUCATION -->
            <tr class="section-label"><td colspan="2">&#127891; Educational Background</td></tr>
            <tr class="divider"><td colspan="2"></td></tr>

            <tr>
                <td class="label">Qualification<br><small style="color:#80C4C0;">(may choose more than one)</small></td>
                <td>
                    <div class="check-group">
                        <label><input type="checkbox" name="edu" value="SPM"> SPM</label>
                        <label><input type="checkbox" name="edu" value="Diploma"> Diploma</label>
                        <label><input type="checkbox" name="edu" value="Degree"> Degree</label>
                        <label><input type="checkbox" name="edu" value="Master"> Master</label>
                        <label><input type="checkbox" name="edu" value="PhD"> PhD</label>
                    </div>
                    <div class="error-msg" id="err_edu">&#9888; Please select at least one qualification.</div>
                </td>
            </tr>

            <tr class="divider"><td colspan="2"></td></tr>

            <!-- ACCOUNT INFO -->
            <tr class="section-label"><td colspan="2">&#128274; Account Information</td></tr>
            <tr class="divider"><td colspan="2"></td></tr>

            <tr>
                <td class="label">Username</td>
                <td>
                    <input type="text" id="username" name="username" placeholder="Choose a username">
                    <div class="error-msg" id="err_username">&#9888; Username is required.</div>
                </td>
            </tr>

            <tr>
                <td class="label">Password</td>
                <td>
                    <input type="password" id="password" name="password" placeholder="Min. 6 characters">
                    <div class="error-msg" id="err_password">&#9888; Password must be at least 6 characters.</div>
                </td>
            </tr>

            <tr>
                <td class="label">Re-Enter Password</td>
                <td>
                    <input type="password" id="repassword" name="repassword" placeholder="Confirm your password">
                    <div class="error-msg" id="err_repassword">&#9888; Passwords do not match.</div>
                </td>
            </tr>

            <tr class="divider"><td colspan="2"></td></tr>

            <!-- BUTTONS -->
            <tr class="btn-row">
                <td colspan="2">
                    <button type="submit" class="btn-submit">Submit</button>
                    <button type="button" class="btn-clear" onclick="clearSignup()">Clear</button>
                    <button type="button" class="btn-cancel" onclick="window.location.href='login.php'">Cancel</button>
                </td>
            </tr>

        </table>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>

    </div>
</div>

<script>
    function validateSignup() {
        let isValid = true;

        function check(id, errId, condition) {
            const el = document.getElementById(id);
            const err = document.getElementById(errId);
            if (condition) {
                el.classList.add('input-error');
                err.classList.add('show');
                isValid = false;
            } else {
                el.classList.remove('input-error');
                err.classList.remove('show');
            }
        }

        document.querySelectorAll('.error-msg').forEach(e => e.classList.remove('show'));
        document.querySelectorAll('.input-error').forEach(e => e.classList.remove('input-error'));

        check('first_name', 'err_first', document.getElementById('first_name').value.trim() === '');
        check('last_name', 'err_last', document.getElementById('last_name').value.trim() === '');

        const genderSelected = document.querySelector('input[name="gender"]:checked');
        if (!genderSelected) {
            document.getElementById('err_gender').classList.add('show');
            isValid = false;
        }

        check('phone', 'err_phone', document.getElementById('phone').value.trim() === '');

        const email = document.getElementById('email').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        check('email', 'err_email', !emailRegex.test(email));

        check('address1', 'err_address1', document.getElementById('address1').value.trim() === '');
        check('city', 'err_city', document.getElementById('city').value.trim() === '');
        check('state', 'err_state', document.getElementById('state').value === '');

        const eduChecked = document.querySelectorAll('input[name="edu"]:checked');
        if (eduChecked.length === 0) {
            document.getElementById('err_edu').classList.add('show');
            isValid = false;
        }

        check('username', 'err_username', document.getElementById('username').value.trim() === '');

        const password = document.getElementById('password').value;
        check('password', 'err_password', password.length < 6);

        const repassword = document.getElementById('repassword').value;
        check('repassword', 'err_repassword', repassword !== password || repassword === '');

        return isValid;
    }

    function clearSignup() {
        document.getElementById('signupForm').reset();
        document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        document.querySelectorAll('.error-msg').forEach(el => el.classList.remove('show'));
    }
</script>

<!-- FOOTER -->
<footer>
    <a href="disclaimer.html">Disclaimer &amp; Copyright</a>
    &copy; 2026 Nur Aisha Binti Noralimin. All rights reserved.
</footer>

</body>
</html>
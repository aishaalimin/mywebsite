<?php
session_start();

if (isset($_SESSION['admin'])) {
    header("Location: admin/dashboard.php");
    exit();
}

if (isset($_SESSION['user'])) {
    header("Location: index.htm");
    exit();
}

$error = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'timeout') {
        $error = "Session expired due to inactivity. Please login again.";
    } elseif ($_GET['msg'] == 'logout') {
        $error = "You have been logged out successfully.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php';

    $username = $_POST['username'];
    $password = MD5($_POST['password']);

    // Check admin table first
    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin'] = $row['username'];
        $_SESSION['full_name'] = $row['full_name'];
        $_SESSION['last_activity'] = time();
        header("Location: admin/dashboard.php");
        exit();
    } else {
        // Check users table
        $query2 = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $result2 = mysqli_query($conn, $query2);

        if (mysqli_num_rows($result2) == 1) {
            $row2 = mysqli_fetch_assoc($result2);
            $_SESSION['user'] = $row2['username'];
            $_SESSION['user_name'] = $row2['first_name'] . ' ' . $row2['last_name'];
            header("Location: index.htm");
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - My Personal Website</title>
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

        .login-wrapper {
            display: flex; justify-content: center;
            align-items: center; min-height: 70vh; padding: 40px 20px;
        }

        .login-box {
            background: white; border: 2px solid #008080;
            border-radius: 14px; padding: 40px; width: 100%;
            max-width: 480px; box-shadow: 0 6px 24px rgba(0,128,128,0.15);
        }

        .login-box h2 { text-align: center; color: #00494C; font-size: 1.5em; margin-bottom: 8px; }
        .login-box p.subtitle { text-align: center; color: #004D4D; font-size: 13px; margin-bottom: 30px; }

        .login-table { width: 100%; border-collapse: collapse; }
        .login-table td { padding: 10px 6px; vertical-align: middle; font-size: 15px; color: #003333; }
        .login-table td:first-child { font-weight: bold; color: #008080; width: 110px; white-space: nowrap; }

        .login-table input[type="text"],
        .login-table input[type="password"] {
            width: 100%; padding: 10px 14px;
            border: 2px solid #80C4C0; border-radius: 8px;
            font-size: 14px; color: #003333;
            background: #F5FFFA; outline: none; transition: border 0.2s;
        }
        .login-table input:focus { border-color: #008080; }

        .input-error { border-color: #e53e3e !important; background: #fff5f5 !important; }

        .error-msg {
            color: #e53e3e; font-size: 12px;
            margin-top: 4px; display: none; padding-left: 4px;
        }
        .error-msg.show { display: block; }

        .php-error {
            background: #fff5f5; border: 2px solid #e53e3e;
            border-radius: 8px; padding: 12px; text-align: center;
            color: #e53e3e; font-size: 14px; margin-bottom: 16px;
        }

        .divider td { padding: 6px 0; }
        .btn-row td { text-align: center; padding-top: 16px; }

        .btn-submit {
            background: #008080; color: #F5FFFA; border: none;
            padding: 10px 28px; border-radius: 8px; font-size: 14px;
            font-weight: bold; cursor: pointer; margin: 0 5px; transition: background 0.2s;
        }
        .btn-submit:hover { background: #00494C; }

        .btn-clear {
            background: #E8F8F5; color: #008080; border: 2px solid #008080;
            padding: 10px 28px; border-radius: 8px; font-size: 14px;
            font-weight: bold; cursor: pointer; margin: 0 5px; transition: background 0.2s;
        }
        .btn-clear:hover { background: #80C4C0; color: white; }

        .btn-cancel {
            background: #f0f0f0; color: #555; border: 2px solid #ccc;
            padding: 10px 28px; border-radius: 8px; font-size: 14px;
            font-weight: bold; cursor: pointer; margin: 0 5px; transition: background 0.2s;
        }
        .btn-cancel:hover { background: #ddd; }

        .links-row td { text-align: center; padding-top: 18px; font-size: 13px; color: #004D4D; }
        .links-row a { color: #008080; text-decoration: none; font-weight: bold; }
        .links-row a:hover { text-decoration: underline; }
        .links-row span { margin: 0 8px; color: #80C4C0; }

        footer { background: #00494C; color: #F5FFFA; text-align: center; padding: 18px; margin-top: 0; font-size: 14px; }
        footer a { color: #00CED1; text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <a href="index.htm"><img id="logo" src="pic/logo.jpg" alt="My Logo"></a>
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
<div class="page-title">Login</div>

<div class="login-wrapper">
    <div class="login-box">

        <h2>&#128274; Welcome Back</h2>
        <p class="subtitle">Please enter your username and password to login</p>

        <?php if ($error != ''): ?>
        <div class="php-error">&#9888; <?php echo $error; ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="" onsubmit="return validateLogin()">
        <table class="login-table">

            <tr>
                <td>Username</td>
                <td>
                    <input type="text" id="username" name="username" placeholder="Enter your username">
                    <div class="error-msg" id="err_id">&#9888; Username is required.</div>
                </td>
            </tr>

            <tr>
                <td>Password</td>
                <td>
                    <input type="password" id="password" name="password" placeholder="Enter your password">
                    <div class="error-msg" id="err_password">&#9888; Password is required.</div>
                </td>
            </tr>

            <tr class="divider"><td colspan="2"></td></tr>

            <tr class="btn-row">
                <td colspan="2">
                    <button type="submit" class="btn-submit">Submit</button>
                    <button type="button" class="btn-clear" onclick="clearLogin()">Clear</button>
                    <button type="button" class="btn-cancel" onclick="window.location.href='index.htm'">Cancel</button>
                </td>
            </tr>

            <tr class="links-row">
                <td colspan="2">
                    <a href="forgot_password.php">Forgot Password?</a>
                    <span>|</span>
                    <a href="signup.php">Sign Up</a>
                </td>
            </tr>

        </table>
        </form>

    </div>
</div>

<script>
    function validateLogin() {
        let isValid = true;

        const username = document.getElementById('username');
        const password = document.getElementById('password');
        const errId = document.getElementById('err_id');
        const errPassword = document.getElementById('err_password');

        username.classList.remove('input-error');
        password.classList.remove('input-error');
        errId.classList.remove('show');
        errPassword.classList.remove('show');

        if (username.value.trim() === '') {
            username.classList.add('input-error');
            errId.classList.add('show');
            isValid = false;
        }

        if (password.value.trim() === '') {
            password.classList.add('input-error');
            errPassword.classList.add('show');
            isValid = false;
        }

        return isValid;
    }

    function clearLogin() {
        document.getElementById('username').value = '';
        document.getElementById('password').value = '';
        document.getElementById('username').classList.remove('input-error');
        document.getElementById('password').classList.remove('input-error');
        document.getElementById('err_id').classList.remove('show');
        document.getElementById('err_password').classList.remove('show');
    }
</script>

<!-- FOOTER -->
<footer>
    <a href="disclaimer.html">Disclaimer &amp; Copyright</a>
    &copy; 2026 Nur Aisha Binti Noralimin. All rights reserved.
</footer>

</body>
</html>
<?php
session_start();

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 60)) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?msg=timeout");
    exit();
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username  = trim($_POST['username']);
    $password  = trim($_POST['password']);
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);

    // Check if username already exists
    $check = mysqli_query($conn, "SELECT id FROM admin WHERE username='$username'");

    if (mysqli_num_rows($check) > 0) {
        $error = "Username already exists! Please choose another.";
    } elseif (empty($username) || empty($password) || empty($full_name) || empty($email)) {
        $error = "All fields are required!";
    } else {
        $hashed = MD5($password);
        mysqli_query($conn, "INSERT INTO admin (username, password, full_name, email)
                             VALUES ('$username', '$hashed', '$full_name', '$email')");
        $success = "Admin added successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #F5FFFA; color: #003333; }

        .topbar {
            background: #00494C; color: #F5FFFA;
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 30px;
        }
        .topbar h1 { font-size: 1.3em; }
        .topbar a {
            background: #e53e3e; color: white; text-decoration: none;
            padding: 8px 18px; border-radius: 6px; font-size: 13px;
            font-weight: bold; margin-left: 16px;
        }
        .topbar a:hover { background: #c53030; }
        .topbar .admin-info { font-size: 14px; color: #A8DCDA; }

        nav { background: #008080; text-align: center; padding: 12px; }
        nav a { color: #F5FFFA; text-decoration: none; margin: 0 14px; font-weight: bold; font-size: 15px; }
        nav a:hover { color: #00CED1; text-decoration: underline; }

        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }

        .form-card {
            background: white;
            border: 2px solid #008080;
            border-radius: 14px;
            padding: 40px;
            box-shadow: 0 6px 24px rgba(0,128,128,0.15);
        }
        .form-card h2 {
            color: #00494C; font-size: 1.4em;
            margin-bottom: 24px; text-align: center;
        }

        .alert {
            padding: 12px 18px; border-radius: 8px;
            margin-bottom: 20px; font-size: 14px; font-weight: bold;
        }
        .alert-success { background: #E8F8F5; border: 2px solid #008080; color: #00494C; }
        .alert-error { background: #fff5f5; border: 2px solid #e53e3e; color: #e53e3e; }

        .form-table { width: 100%; border-collapse: collapse; }
        .form-table td { padding: 10px 6px; vertical-align: middle; font-size: 14px; }
        .form-table td:first-child {
            font-weight: bold; color: #008080;
            width: 130px; white-space: nowrap;
        }

        .form-table input[type="text"],
        .form-table input[type="email"],
        .form-table input[type="password"] {
            width: 100%; padding: 10px 14px;
            border: 2px solid #80C4C0; border-radius: 8px;
            font-size: 14px; color: #003333;
            background: #F5FFFA; outline: none; transition: border 0.2s;
        }
        .form-table input:focus { border-color: #008080; }

        .btn-row td { text-align: center; padding-top: 20px; }

        .btn-submit {
            background: #008080; color: #F5FFFA; border: none;
            padding: 11px 30px; border-radius: 8px; font-size: 14px;
            font-weight: bold; cursor: pointer; margin: 0 5px; transition: background 0.2s;
        }
        .btn-submit:hover { background: #00494C; }

        .btn-back {
            background: #f0f0f0; color: #555; border: 2px solid #ccc;
            padding: 11px 30px; border-radius: 8px; font-size: 14px;
            font-weight: bold; cursor: pointer; margin: 0 5px;
            text-decoration: none; display: inline-block;
        }
        .btn-back:hover { background: #ddd; }

        footer { background: #00494C; color: #F5FFFA; text-align: center; padding: 18px; margin-top: 40px; font-size: 14px; }
        footer a { color: #00CED1; text-decoration: none; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>&#43; Add New Admin</h1>
    <div style="display:flex; align-items:center;">
        <span class="admin-info">Welcome, <strong><?php echo $_SESSION['full_name']; ?></strong></span>
        <a href="logout.php">&#128274; Logout</a>
    </div>
</div>

<nav>
    <a href="../index.htm">&#127760; Home</a>
    <a href="dashboard.php">&#128187; Dashboard</a>
    <a href="manage.php">&#128100; Manage Admin</a>
</nav>

<div class="container">
    <div class="form-card">
        <h2>&#43; Add New Admin</h2>

        <?php if ($error != ''): ?>
        <div class="alert alert-error">&#9888; <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success != ''): ?>
        <div class="alert alert-success">&#10003; <?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
        <table class="form-table">
            <tr>
                <td>Full Name</td>
                <td><input type="text" name="full_name" placeholder="Enter full name"
                    value="<?php echo isset($_POST['full_name']) ? $_POST['full_name'] : ''; ?>"></td>
            </tr>
            <tr>
                <td>Username</td>
                <td><input type="text" name="username" placeholder="Enter username"
                    value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>"></td>
            </tr>
            <tr>
                <td>Password</td>
                <td><input type="password" name="password" placeholder="Enter password"></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><input type="email" name="email" placeholder="Enter email"
                    value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>"></td>
            </tr>
            <tr class="btn-row">
                <td colspan="2">
                    <button type="submit" class="btn-submit">&#43; Add Admin</button>
                    <a href="manage.php" class="btn-back">&#8592; Back</a>
                </td>
            </tr>
        </table>
        </form>

    </div>
</div>

<footer>
    <a href="../disclaimer.html">Disclaimer &amp; Copyright</a>
    &copy; 2026 Nur Aisha Binti Noralimin. All rights reserved.
</footer>

</body>
</html>
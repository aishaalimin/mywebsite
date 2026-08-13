<?php
session_start();

// Check session timeout — 1 minute
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 60)) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?msg=timeout");
    exit();
}
$_SESSION['last_activity'] = time();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #F5FFFA; color: #003333; }

        /* TOP BAR */
        .topbar {
            background: #00494C;
            color: #F5FFFA;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
        }
        .topbar h1 { font-size: 1.3em; }
        .topbar .admin-info { font-size: 14px; color: #A8DCDA; }
        .topbar a {
            background: #e53e3e;
            color: white;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            margin-left: 16px;
            transition: background 0.2s;
        }
        .topbar a:hover { background: #c53030; }

        /* NAV */
        nav { background: #008080; text-align: center; padding: 12px; }
        nav a { color: #F5FFFA; text-decoration: none; margin: 0 14px; font-weight: bold; font-size: 15px; }
        nav a:hover { color: #00CED1; text-decoration: underline; }
        nav a.active { color: #00CED1; border-bottom: 2px solid #00CED1; padding-bottom: 2px; }

        /* CONTENT */
        .container { max-width: 960px; margin: 40px auto; padding: 0 20px; }

        .welcome-card {
            background: white;
            border: 2px solid #008080;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
        }
        .welcome-card h2 { color: #00494C; font-size: 1.6em; margin-bottom: 8px; }
        .welcome-card p { color: #004D4D; font-size: 14px; }

        /* STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border: 2px solid #008080;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            text-decoration: none;
            color: #003333;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 18px rgba(0,128,128,0.2);
        }
        .stat-card .icon { font-size: 2.5em; margin-bottom: 10px; display: block; }
        .stat-card h3 { color: #00494C; font-size: 1.1em; margin-bottom: 6px; }
        .stat-card p { color: #008080; font-size: 13px; }

        /* SESSION TIMER */
        .session-bar {
            background: #E8F8F5;
            border: 2px solid #008080;
            border-radius: 8px;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #004D4D;
        }
        .session-bar span { font-weight: bold; color: #008080; }
        #timer { color: #e53e3e; font-weight: bold; font-size: 1.1em; }

        footer { background: #00494C; color: #F5FFFA; text-align: center; padding: 18px; margin-top: 40px; font-size: 14px; }
        footer a { color: #00CED1; text-decoration: none; }
    </style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <h1>&#128187; Admin Dashboard</h1>
    <div style="display:flex; align-items:center;">
        <span class="admin-info">Welcome, <strong><?php echo $_SESSION['full_name']; ?></strong></span>
        <a href="logout.php">&#128274; Logout</a>
    </div>
</div>

<!-- NAV -->
<nav>
    <a href="../index.htm">&#127760; Home</a>
    <a href="dashboard.php" class="active">&#128187; Dashboard</a>
    <a href="manage.php">&#128100; Manage Admin</a>
</nav>

<div class="container">

    <!-- WELCOME -->
    <div class="welcome-card">
        <h2>&#128075; Hello, <?php echo $_SESSION['full_name']; ?>!</h2>
        <p>You are logged in as <strong><?php echo $_SESSION['admin']; ?></strong>. 
        Your session will expire after <strong>1 minute</strong> of inactivity.</p>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
        <a href="manage.php" class="stat-card">
            <span class="icon">&#128100;</span>
            <h3>Manage Admin</h3>
            <p>View, add, edit and delete admin accounts</p>
        </a>
        <a href="../index.htm" class="stat-card">
            <span class="icon">&#127760;</span>
            <h3>View Website</h3>
            <p>Go back to the public website</p>
        </a>
        <a href="logout.php" class="stat-card">
            <span class="icon">&#128274;</span>
            <h3>Logout</h3>
            <p>End your admin session safely</p>
        </a>
    </div>

    <!-- SESSION TIMER -->
    <div class="session-bar">
        <span>&#9201; Session timeout in:</span>
        <span id="timer">60</span> seconds
        <span style="font-size:12px; color:#666;">— Move your mouse or click to reset</span>
    </div>

</div>

<footer>
    <a href="../disclaimer.html">Disclaimer &amp; Copyright</a>
    &copy; 2026 Nur Aisha Binti Noralimin. All rights reserved.
</footer>

<script>
    // Countdown timer — resets on any activity
    let timeLeft = 60;
    const timerEl = document.getElementById('timer');

    function resetTimer() {
        timeLeft = 60;
        // Also ping server to reset session
        fetch('session_ping.php');
    }

    document.addEventListener('mousemove', resetTimer);
    document.addEventListener('keypress', resetTimer);
    document.addEventListener('click', resetTimer);

    setInterval(() => {
        timeLeft--;
        timerEl.textContent = timeLeft;
        if (timeLeft <= 10) timerEl.style.color = '#e53e3e';
        else timerEl.style.color = '#008080';
        if (timeLeft <= 0) {
            window.location.href = '../login.php?msg=timeout';
        }
    }, 1000);
</script>

</body>
</html>
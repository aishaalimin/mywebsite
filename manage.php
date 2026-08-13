<?php
session_start();

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 60)) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?msg=timeout");
    exit();
}
$_SESSION['last_activity'] = time();

// Check if logged in
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// DELETE admin
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Prevent deleting yourself
    $check = mysqli_query($conn, "SELECT username FROM admin WHERE id='$id'");
    $row = mysqli_fetch_assoc($check);
    if ($row['username'] == $_SESSION['admin']) {
        $msg = "error|You cannot delete your own account!";
    } else {
        mysqli_query($conn, "DELETE FROM admin WHERE id='$id'");
        $msg = "success|Admin deleted successfully!";
    }
}

// Get all admins
$admins = mysqli_query($conn, "SELECT * FROM admin ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Admin</title>
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
            font-weight: bold; margin-left: 16px; transition: background 0.2s;
        }
        .topbar a:hover { background: #c53030; }
        .topbar .admin-info { font-size: 14px; color: #A8DCDA; }

        nav { background: #008080; text-align: center; padding: 12px; }
        nav a { color: #F5FFFA; text-decoration: none; margin: 0 14px; font-weight: bold; font-size: 15px; }
        nav a:hover { color: #00CED1; text-decoration: underline; }
        nav a.active { color: #00CED1; border-bottom: 2px solid #00CED1; padding-bottom: 2px; }

        .container { max-width: 960px; margin: 40px auto; padding: 0 20px; }

        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .page-heading h2 { color: #00494C; font-size: 1.4em; }

        .btn-add {
            background: #008080; color: #F5FFFA;
            text-decoration: none; padding: 10px 22px;
            border-radius: 8px; font-weight: bold;
            font-size: 14px; transition: background 0.2s;
        }
        .btn-add:hover { background: #00494C; }

        /* ALERT MESSAGES */
        .alert {
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .alert-success {
            background: #E8F8F5;
            border: 2px solid #008080;
            color: #00494C;
        }
        .alert-error {
            background: #fff5f5;
            border: 2px solid #e53e3e;
            color: #e53e3e;
        }

        /* TABLE */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 2px solid #008080;
            border-radius: 10px;
            overflow: hidden;
        }
        .admin-table th {
            background: #008080;
            color: #F5FFFA;
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
        }
        .admin-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #A8DCDA;
            font-size: 14px;
            color: #003333;
            vertical-align: middle;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:nth-child(even) td { background: #E8F8F5; }
        .admin-table tr:hover td { background: #d0f0ea; }

        /* ACTION BUTTONS */
        .btn-edit {
            background: #008080; color: white;
            text-decoration: none; padding: 6px 14px;
            border-radius: 6px; font-size: 13px;
            font-weight: bold; margin-right: 6px;
            transition: background 0.2s;
        }
        .btn-edit:hover { background: #00494C; }

        .btn-delete {
            background: #e53e3e; color: white;
            text-decoration: none; padding: 6px 14px;
            border-radius: 6px; font-size: 13px;
            font-weight: bold; transition: background 0.2s;
            border: none; cursor: pointer;
        }
        .btn-delete:hover { background: #c53030; }

        /* BADGE */
        .badge {
            background: #E8F8F5;
            color: #008080;
            border: 1px solid #80C4C0;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-you {
            background: #00494C;
            color: #F5FFFA;
        }

        footer { background: #00494C; color: #F5FFFA; text-align: center; padding: 18px; margin-top: 40px; font-size: 14px; }
        footer a { color: #00CED1; text-decoration: none; }
    </style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <h1>&#128100; Manage Admin</h1>
    <div style="display:flex; align-items:center;">
        <span class="admin-info">Welcome, <strong><?php echo $_SESSION['full_name']; ?></strong></span>
        <a href="logout.php">&#128274; Logout</a>
    </div>
</div>

<!-- NAV -->
<nav>
    <a href="../index.htm">&#127760; Home</a>
    <a href="dashboard.php">&#128187; Dashboard</a>
    <a href="manage.php" class="active">&#128100; Manage Admin</a>
</nav>

<div class="container">

    <div class="page-heading">
        <h2>&#128100; Admin Accounts</h2>
        <a href="add.php" class="btn-add">&#43; Add New Admin</a>
    </div>

    <!-- ALERT MESSAGE -->
    <?php if (isset($msg)):
        $parts = explode('|', $msg);
        $type = $parts[0];
        $text = $parts[1];
    ?>
    <div class="alert alert-<?php echo $type; ?>">
        <?php echo $type == 'success' ? '&#10003;' : '&#9888;'; ?> <?php echo $text; ?>
    </div>
    <?php endif; ?>

    <!-- ADMIN TABLE -->
    <table class="admin-table">
        <tr>
            <th>#</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>

        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($admins)):
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td>
                <?php echo $row['username']; ?>
                <?php if ($row['username'] == $_SESSION['admin']): ?>
                <span class="badge badge-you">You</span>
                <?php endif; ?>
            </td>
            <td><?php echo $row['full_name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">&#9998; Edit</a>
                <?php if ($row['username'] != $_SESSION['admin']): ?>
                <button class="btn-delete"
                    onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo $row['username']; ?>')">
                    &#128465; Delete
                </button>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>

</div>

<footer>
    <a href="../disclaimer.html">Disclaimer &amp; Copyright</a>
    &copy; 2026 Nur Aisha Binti Noralimin. All rights reserved.
</footer>

<script>
    function confirmDelete(id, username) {
        if (confirm('Are you sure you want to delete admin "' + username + '"?')) {
            window.location.href = 'manage.php?delete=' + id;
        }
    }
</script>

</body>
</html>
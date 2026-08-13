<?php
session_start();

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 60)) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?msg=timeout");
    exit();
}

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $check = mysqli_query($conn, "SELECT username FROM admin WHERE id='$id'");
    $row = mysqli_fetch_assoc($check);
    if ($row['username'] == $_SESSION['admin']) {
        header("Location: manage.php?msg=error|You cannot delete your own account!");
    } else {
        mysqli_query($conn, "DELETE FROM admin WHERE id='$id'");
        header("Location: manage.php?msg=success|Admin deleted successfully!");
    }
} else {
    header("Location: manage.php");
}
exit();
?>
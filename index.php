<?php
session_start();

// If user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: myadmin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

// If not logged in, redirect to login page
header("Location: login.php");
exit();
?>

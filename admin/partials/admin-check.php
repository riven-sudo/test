<?php
include('login-check.php'); // make sure user is logged in first

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['no-access'] = "<div class='error text-center'>Access Denied. Admins only.</div>";
    header('location:'.SITEURL.'admin/index.php');
    exit();
}
?>

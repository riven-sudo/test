<?php 
include('../config/constants.php'); 
include('login-check.php');
?>


<html>
<head>
    <title>BLACKSTAR - Admin page</title>
    <link rel="stylesheet" href="../css/admin.css">
    <!-- Font Awesome for action icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-p1CmQm3q2v1t2wV0K3Qk7YQeYJQ6K4m3K8Jm0e4Y3dG5qZ9Vb6XqKq3nX2z2W1yF0Z6qYpJ7rF6b4J1Lr5zDA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar Starts -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <a href="index.php">
                    <img src="../images/logo.png" alt="Blackstar Logo" class="sidebar-logo">
                </a>
                <a href="index.php" class="brand-text">BLACKSTAR</a>
                <div class="since">Since 2011</div>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php">Dashboard</a></li>

                    <?php if($_SESSION['role'] == 'admin') { ?>
                        <li><a href="manage-admin.php">Admins</a></li>
                        <li><a href="manage-customer.php">Customers</a></li>
                        <li><a href="manage-category.php">Categories</a></li>
                        <li><a href="manage-food.php">Foods</a></li>
                    <?php } ?>

                    <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'kitchen') { ?>
                        <li><a href="manage-order-dinein.php">Dine-In Orders</a></li>
                        <li><a href="manage-order-takeout.php">Take-Out Orders</a></li>
                    <?php } ?>

                    <?php if($_SESSION['role'] == 'delivery') { ?>
                        <li><a href="manage-order-takeout.php">Take-Out Orders</a></li>
                        <li><a href="manage-order-dinein.php">Dine-In Orders</a></li>
                    <?php } ?>

                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>
        <!-- Sidebar Ends -->

        <!-- Main area starts - individual pages should render inside this -->
        <div class="main-area">
            <!-- pages include their own .main-content or wrapper sections here -->
            
<!-- end of menu.php - page content continues in the included file -->

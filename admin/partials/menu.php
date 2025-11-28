<?php 
include('../config/constants.php'); 
include('login-check.php');
?>


<html>
<head>
    <title>BLACKSTAR - Admin page</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <!--Menu Section Starts-->
    <div class="menu text-center">
        <div class="wrapper">
            <ul>
                <li><a href="index.php">Home</a></li>

                <?php if($_SESSION['role'] == 'admin') { ?>
                    <li><a href="manage-admin.php">Admin</a></li>
                    <li><a href="manage-customer.php">Customer</a></li>
                    <li><a href="manage-category.php">Category</a></li>
                    <li><a href="manage-food.php">Food</a></li>
                    
                <?php } ?>

                <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'kitchen') { ?>
                   <li><a href="manage-order-dinein.php" onclick="return chooseOrder();">Order</a></li>
                <?php } ?>

                <?php if($_SESSION['role'] == 'delivery') { ?>
                    <li><a href="manage-order-dinein.php" onclick="return chooseOrder();">Order</a></li>
                <?php } ?>

                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
    <!--Menu Section Ends-->

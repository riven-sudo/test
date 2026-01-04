<?php
    // Include constants for SITEURL and DB connection
    include('../config/constants.php');
     include('partials/admin-check.php'); 

    // Check if ID is set
    if(isset($_GET['id']))
    {
        $id = $_GET['id'];

        // SQL to delete order
        $sql = "DELETE FROM tbl_takeout WHERE id=$id";

        // Execute query
        $res = mysqli_query($conn, $sql);

        if($res == true)
        {
            // Success
            $_SESSION['delete'] = "<div class='success'>Order Deleted Successfully.</div>";
            header('location:'.SITEURL.'admin/manage-order-takeout.php');
        }
        else
        {
            // Failed
            $_SESSION['delete'] = "<div class='error'>Failed to Delete Order.</div>";
            header('location:'.SITEURL.'admin/manage-order-takeout.php');
        }
    }
    else
    {
        // Redirect if no ID
        header('location:'.SITEURL.'admin/manage-order-takeout.php');
    }
?>

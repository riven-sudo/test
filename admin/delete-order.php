
<?php
    include('../config/constants.php');
    include('partials/admin-check.php'); 
    

    // Check if ID is set
    if(isset($_GET['id']))
    {
        $id = $_GET['id'];

        // Fetch old row for audit
        $old_row = null;
        $sel_old = mysqli_query($conn, "SELECT * FROM tbl_order WHERE id=$id LIMIT 1");
        if ($sel_old && mysqli_num_rows($sel_old) == 1) $old_row = mysqli_fetch_assoc($sel_old);

        // SQL to delete order
        $sql = "DELETE FROM tbl_order WHERE id=$id";

        // Execute query
        $res = mysqli_query($conn, $sql);

        if($res == true)
        {
            // Success
            $_SESSION['delete'] = "<div class='success'>Order Deleted Successfully.</div>";
            @Audit::log('delete_order', 'tbl_order', $id, $old_row, null);
            header('location:'.SITEURL.'admin/manage-order-dinein.php');
        }
        else
        {
            // Failed
            $_SESSION['delete'] = "<div class='error'>Failed to Delete Order.</div>";
            @Audit::log('delete_order_failed', 'tbl_order', $id, $old_row, null);
            header('location:'.SITEURL.'admin/manage-order-dinein.php');
        }
    }
    else
    {
        // Redirect if no ID
        header('location:'.SITEURL.'admin/manage-order-dinein.php');
    }
?>

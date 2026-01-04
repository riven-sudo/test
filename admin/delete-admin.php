<?php
    //include constants.php file here
    include('../config/constants.php');
     include('partials/admin-check.php')

    //Get the ID of Admin to be deleted
    $id = $_GET['id'];

    // Fetch old admin row for audit
    $old_row = null;
    $sel_old = mysqli_query($conn, "SELECT * FROM tbl_admin WHERE id=$id LIMIT 1");
    if ($sel_old && mysqli_num_rows($sel_old) == 1) $old_row = mysqli_fetch_assoc($sel_old);

    //Create SQL Query to Delete Admin
    $sql = "DELETE FROM tbl_admin WHERE id=$id";

    //Execute the Query
    $res = mysqli_query($conn, $sql);

    //Check whether the Query Executed Successfully or not
    if($res==true)
    {
        //Query executed successfully and Admin Deleted
        //echo "admin deleted";
        //Create Session Variable to display Message 
        $_SESSION['delete'] = "<div class='success'>Admin Deleted Successfully</div>";
        @Audit::log('delete_admin', 'tbl_admin', $id, $old_row, null);
        //Redirect to Manage Admin page
        header('location:'.SITEURL.'admin/manage-admin.php');
    }
    else
    {
        //Failed to Delete Admin
        //echo "failed to delete admin";

        $_SESSION['delete'] = "<div class='error'>Failed to Delete Admin. Try Again Later.</div>";
        @Audit::log('delete_admin_failed', 'tbl_admin', $id, $old_row, null);
        header('location:'.SITEURL.'admin/manage-admin.php');
    }
    //Redirect to Manage Admin page with message (Success/error)

?>
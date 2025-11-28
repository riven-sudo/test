<?php
    //include constants.php file here
    include('../config/constants.php');
     include('partials/admin-check.php')

    //Get the ID of Admin to be deleted
    $id = $_GET['id'];

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
        //Redirect to Manage Admin page
        header('location:'.SITEURL.'admin/manage-admin.php');
    }
    else
    {
        //Failed to Delete Admin
        //echo "failed to delete admin";

        $_SESSION['delete'] = "<div class='error'>Failed to Delete Admin. Try Again Later.</div>";
        header('location:'.SITEURL.'admin/manage-admin.php');
    }
    //Redirect to Manage Admin page with message (Success/error)

?>
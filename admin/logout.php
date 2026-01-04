<?php
    //include constants.php fot SITEURL
    include('../config/constants.php');
    //Audit logout event
    $admin_username = $_SESSION['user'] ?? null;
    @Audit::log('admin_logout', 'tbl_admin', null, null, array('username'=>$admin_username));

    //Destroy the Session 
    session_destroy();//Unsets $_SESSION['user']

    //Redirect to Login page
    header('location:'.SITEURL.'admin/login.php');
?>
<?php
    //include constants.php fot SITEURL
    include('../config/constants.php');
    //Destroy the Session 
    session_destroy();//Unsets $_SESSION['user']

    //Redirect to Login page
    header('location:'.SITEURL.'admin/login.php');
?>
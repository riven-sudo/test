<?php
include('config/constants.php');

// Destroy session
session_unset();
session_destroy();

// Redirect to homepage
header("Location: ".SITEURL);
exit();
?>

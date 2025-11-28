<?php
include('../config/constants.php');
include('partials/admin-check.php'); 

// Check if transaction number is set
if(isset($_GET['txn'])){
    $txn = mysqli_real_escape_string($conn, $_GET['txn']);

    // Delete all orders in that transaction
    $sql = "DELETE FROM tbl_order WHERE transaction_number='$txn'";
    $res = mysqli_query($conn, $sql);

    if($res){
        $_SESSION['delete'] = "<div class='success'>All orders under transaction <b>$txn</b> deleted successfully.</div>";
    } else {
        $_SESSION['delete'] = "<div class='error'>Failed to delete orders for transaction <b>$txn</b>.</div>";
    }
} else {
    $_SESSION['delete'] = "<div class='error'>Invalid request.</div>";
}

// Redirect back
header("location:".SITEURL.'admin/manage-order-dinein.php');
exit;
?>

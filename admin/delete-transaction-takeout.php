<?php
include('../config/constants.php');
include('partials/admin-check.php');

// Check if transaction number is set
if (isset($_GET['txn'])) {
    $txn = mysqli_real_escape_string($conn, $_GET['txn']);

    // Collect affected takeout order IDs for audit
    $affected = [];
    $sel = mysqli_query($conn, "SELECT id FROM tbl_takeout WHERE transaction_number='$txn'");
    if ($sel) {
        while ($r = mysqli_fetch_assoc($sel)) $affected[] = $r['id'];
    }

    // Delete all orders in that transaction
    $sql = "DELETE FROM tbl_takeout WHERE transaction_number='$txn'";
    $res = mysqli_query($conn, $sql);

    if ($res) {
        $_SESSION['delete'] = "<div class='success'>All orders under transaction <b>$txn</b> deleted successfully.</div>";
        @Audit::log('delete_transaction_takeout_orders', null, null, array('txn'=>$txn,'order_ids'=>$affected), null, array('count'=>count($affected)));
    } else {
        $_SESSION['delete'] = "<div class='error'>Failed to delete orders for transaction <b>$txn</b>.</div>";
        @Audit::log('delete_transaction_takeout_orders_failed', null, null, array('txn'=>$txn,'order_ids'=>$affected), null);
    }
} else {
    $_SESSION['delete'] = "<div class='error'>Invalid request.</div>";
}

// Redirect back
header("location:" . SITEURL . 'admin/manage-order-takeout.php');
exit;
?>

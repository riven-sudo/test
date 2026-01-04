<?php
include('../../config/constants.php'); // correct path

// Check if id and status are passed in URL
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Sanitize
    $id = mysqli_real_escape_string($conn, $id);
    $status = mysqli_real_escape_string($conn, $status);

    // Fetch old row for audit
    $old_row = null;
    $sel_old = mysqli_query($conn, "SELECT * FROM tbl_customer WHERE id=$id LIMIT 1");
    if ($sel_old && mysqli_num_rows($sel_old) == 1) $old_row = mysqli_fetch_assoc($sel_old);

    // Update query
    $sql = "UPDATE tbl_customer SET status='$status' WHERE id=$id";
    $res = mysqli_query($conn, $sql);

    if ($res) {
        $_SESSION['update'] = "<div class='success'>Customer status updated to $status successfully.</div>";
        @Audit::log('update_customer_status', 'tbl_customer', $id, $old_row, array('status'=>$status));
    } else {
        $_SESSION['update'] = "<div class='error'>Failed to update customer status.</div>";
        @Audit::log('update_customer_status_failed', 'tbl_customer', $id, $old_row, array('status'=>$status));
    }
} else {
    $_SESSION['update'] = "<div class='error'>Invalid request.</div>";
}

// Redirect back to manage-customer.php
header('location:'.SITEURL.'admin/manage-customer.php');
exit();
?>

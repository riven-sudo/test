<?php
include('../config/constants.php'); // adjust path if needed

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    $old_row = null;
    $sel_old = mysqli_query($conn, "SELECT * FROM tbl_customer WHERE id = $id LIMIT 1");
    if ($sel_old && mysqli_num_rows($sel_old) == 1) $old_row = mysqli_fetch_assoc($sel_old);

    $sql = "DELETE FROM tbl_customer WHERE id = $id";
    $res = mysqli_query($conn, $sql);

    if($res) {
        $_SESSION['delete'] = "<div class='success'>Customer deleted successfully.</div>";
        @Audit::log('delete_customer', 'tbl_customer', $id, $old_row, null);
    } else {
        $_SESSION['delete'] = "<div class='error'>Failed to delete customer.</div>";
        @Audit::log('delete_customer_failed', 'tbl_customer', $id, $old_row, null);
    }
}

header("Location: manage-customer.php"); // redirect back to manage page
exit;
?>

<?php
include('../config/constants.php'); // adjust path if needed

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM tbl_customer WHERE id = $id";
    $res = mysqli_query($conn, $sql);

    if($res) {
        $_SESSION['delete'] = "<div class='success'>Customer deleted successfully.</div>";
    } else {
        $_SESSION['delete'] = "<div class='error'>Failed to delete customer.</div>";
    }
}

header("Location: manage-customer.php"); // redirect back to manage page
exit;
?>

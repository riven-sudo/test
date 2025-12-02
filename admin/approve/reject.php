<?php
include('../../config/constants.php'); // correct path

// Check if id and status are passed in URL
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Sanitize
    $id = mysqli_real_escape_string($conn, $id);
    $status = mysqli_real_escape_string($conn, $status);

    // Update query
    $sql = "UPDATE tbl_customer SET status='$status' WHERE id=$id";
    $res = mysqli_query($conn, $sql);

    if ($res) {
        $_SESSION['update'] = "<div class='success'>Customer status updated to $status successfully.</div>";
    } else {
        $_SESSION['update'] = "<div class='error'>Failed to update customer status.</div>";
    }
} else {
    $_SESSION['update'] = "<div class='error'>Invalid request.</div>";
}

// Redirect back to manage-customer.php
header('location:'.SITEURL.'admin/manage-customer.php');
exit();
?>

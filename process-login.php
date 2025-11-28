<?php
include('config/constants.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Find user in DB
    $sql = "SELECT * FROM tbl_customer WHERE username='$username' LIMIT 1";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);

        // Verify password
        if (password_verify($password, $row['password'])) {
            // Check status
            if ($row['status'] === 'Approved') {
                // Start session
                $_SESSION['customer'] = $row['username'];
                $_SESSION['customer_id'] = $row['id'];
                $_SESSION['is_member'] = $row['is_member'];   //  this
                $_SESSION['discount'] = $row['discount'];     // d this

                // Success redirect
                header("Location: ".SITEURL."index.php");
                exit();
            } elseif ($row['status'] === 'Pending') {
                $_SESSION['login'] = "<div class='error'>Your account is still pending on admin's approval.</div>";
            } else {
                $_SESSION['login'] = "<div class='error'>Your account has been rejected.</div>";
            }
        } else {
            $_SESSION['login'] = "<div class='error'>Invalid password.</div>";
        }
    } else {
        $_SESSION['login'] = "<div class='error'>No account found with that username.</div>";
    }

    // Redirect back to login page
    header("Location: ".SITEURL."customer-login.php");
    exit();
}
?>

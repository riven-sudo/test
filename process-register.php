<?php
include('config/constants.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check password match
    if ($password !== $confirm_password) {
        echo "<script>
            alert('Passwords do not match!');
            window.location.href='customer-login.php';
        </script>";
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into DB
    $sql = "INSERT INTO tbl_customer 
        (username, email, password, status, is_member, discount, google_id, profile_pic, name)
        VALUES ('$username', '$email', '$hashedPassword', 'Pending', 1, 5, NULL, NULL, NULL)";

    // RUN QUERY (this was missing)
    $res = mysqli_query($conn, $sql);

    // Check result
    if ($res) {
        echo "<script>
            alert('Registration submitted! You are now a member and will get 5% off once approved by admin.');
            window.location.href='customer-login.php';
        </script>";
        exit();
    } else {
        echo "<script>
            alert('Registration failed. Please try again.');
            window.location.href='customer-login.php';
        </script>";
        exit();
    }
}
?>

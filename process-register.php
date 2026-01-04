<?php
include('config/constants.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($password !== $confirm_password) {
        echo "<!DOCTYPE html>
        <html><head><meta name='viewport' content='width=device-width, initial-scale=1.0'></head>
        <body>
        <script>
            alert('Passwords do not match!');
            window.location.href='customer-login.php';
        </script>
        </body></html>";
        exit();
    }

    // Hash password (you can change to MD5 if you want)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into DB
    $sql = "INSERT INTO tbl_customer (username, email, password, status, is_member, discount) 
            VALUES ('$username', '$email', '$hashedPassword', 'Pending', 1, 5)";
    $res = mysqli_query($conn, $sql);

    if ($res) {
        // Audit registration success (do not log password)
        $inserted_id = mysqli_insert_id($conn);
        $new = array('username' => $username, 'email' => $email, 'status' => 'Pending');
        @Audit::log('customer_register', 'tbl_customer', $inserted_id, null, $new);

        echo "<!DOCTYPE html>
        <html><head><meta name='viewport' content='width=device-width, initial-scale=1.0'></head>
        <body>
        <script>
            alert('Registration submitted! You are now a member and will get 5% off once approved by admin.');
            window.location.href='customer-login.php';
        </script>
        </body></html>";
        exit();
    } else {
        // Audit registration failure
        @Audit::log('customer_register_failed', null, null, null, array('username' => $username, 'email' => $email));

        echo "<!DOCTYPE html>
        <html><head><meta name='viewport' content='width=device-width, initial-scale=1.0'></head>
        <body>
        <script>
            alert('Registration failed. Please try again.');
            window.location.href='customer-login.php';
        </script>
        </body></html>";
        exit();
    }
}
?>

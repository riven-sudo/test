<?php
include('../config/constants.php'); 

?>

<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="login">
        <h1 class="text-center">Login</h1>
        <br><br>

        <?php
            if(isset($_SESSION['login'])) {
                echo $_SESSION['login'];
                unset($_SESSION['login']);
            }

            if(isset($_SESSION['no-login-message'])) {
                echo $_SESSION['no-login-message'];
                unset ($_SESSION['no-login-message']);
            }
        ?>
        <br><br>

        <!-- Login Form Starts here-->
        <form action="" method="POST" class="text-center">
            Username: <br>
            <input type="text" name="username" placeholder="Enter Username" required><br><br>

            Password: <br>
            <input type="password" name="password" placeholder="Enter Password" required><br><br>

            <a href="forgot-password.php">Forgot Password?</a> <br><br>

            <input type="submit" name="submit" value="Login" class="btn-primary">
            <br><br>
        </form>
        <!-- Login Form Ends here-->

        <p class="text-center">Created by - <a href="#">Blackstar</a></p>
    </div>
</body>
</html>

<?php
// Process login on form submit
if(isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Query user
    $sql = "SELECT * FROM tbl_admin WHERE username='$username' LIMIT 1";
    $res = mysqli_query($conn, $sql);

    if($res && mysqli_num_rows($res) == 1) {
        $user = mysqli_fetch_assoc($res);

        // Verify password
        if(password_verify($password, $user['password'])) {
            // Store user session
            $_SESSION['login'] = "<div class='success'>Login Successful</div>";
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role (optional, or just go to dashboard)
            header('location:'.SITEURL.'admin/index.php');
            exit();
        } else {
            $_SESSION['login'] = "<div class='error text-center'>Invalid Username or Password</div>";
            header('location:'.SITEURL.'admin/login.php');
            exit();
        }
    } else {
        $_SESSION['login'] = "<div class='error text-center'>User not found</div>";
        header('location:'.SITEURL.'admin/login.php');
        exit();
    }
}
?>

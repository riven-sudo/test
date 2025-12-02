<?php
include('../config/constants.php'); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/customer-login.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
      /* Small override so admin login box sits nicely on admin pages */
      body { padding-top: 40px; }
      .auth-container { max-width: 420px; }
    </style>
</head>
<body>

<div class="logo-container">
  <a href="<?php echo SITEURL; ?>">
    <img src="../images/background.jpg" alt="Blackstar">
  </a>
</div>

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

<!-- Admin Login Form -->
<div class="auth-container" id="adminLogin">
  <h2>Admin Login</h2>
  <form action="" method="POST">
    <input type="text" name="username" placeholder="Enter Username" required>
    <input type="password" name="password" placeholder="Enter Password" required>
    <div style="text-align:left;margin-top:8px;">
      <a href="forgot-password.php">Forgot Password?</a>
    </div>
    <button type="submit" name="submit" class="glow-btn" style="margin-top:12px;">Login</button>
  </form>
  <div class="toggle-links">
    <p>Back to <a href="../index.php">Website</a></p>
  </div>
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

            // Redirect to dashboard
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

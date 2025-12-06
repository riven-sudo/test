<?php include('config/constants.php');
 ?>
<?php 
if (isset($_SESSION['login'])) {
    echo $_SESSION['login'];
    unset($_SESSION['login']);
}
?>

<?php
require 'google-config.php';
$google_login_url = $client->createAuthUrl();
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Login & Register</title>
  <link rel="stylesheet" href="css/customer-login.css">

  
  
  
</head>
<body>

<div class="logo-container">
  <a href="<?php echo SITEURL; ?>">
    <img src="images/background.jpg" alt="Blackstar">
  </a>
</div>



  <!-- Login Form -->
  <div class="auth-container" id="loginForm">
    <h2>Customer Login</h2>
    <form action="process-login.php" method="POST">
      <input type="text" name="username" placeholder="Enter Username" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <a href="google-login.php">
  <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" 
       alt="Sign in with Google">
</a>

      <button type="submit" class="glow-btn">Login</button>
    </form>
    <div class="toggle-links">
      <p>Don’t have an account? <a onclick="showRegister()">Register</a></p>
    </div>
  </div>

  <!-- Register Form -->
  <div class="auth-container" id="registerForm" style="display:none;">
    <h2>Register</h2>
    <form action="process-register.php" method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Enter Email" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit" class="glow-btn">Register</button>
    </form>
    <div class="toggle-links">
      <p>Already have an account? <a onclick="showLogin()">Login</a></p>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Dark Mode
    const toggleBtn = document.getElementById("darkModeToggle");
    if (localStorage.getItem("dark-mode") === "enabled") {
      document.body.classList.add("dark-mode");
    }
    toggleBtn.addEventListener("click", () => {
      document.body.classList.toggle("dark-mode");
      localStorage.setItem("dark-mode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
    });

    // Toggle Forms
    function showRegister() {
      document.getElementById("loginForm").style.display = "none";
      document.getElementById("registerForm").style.display = "block";
    }
    function showLogin() {
      document.getElementById("registerForm").style.display = "none";
      document.getElementById("loginForm").style.display = "block";
    }
  </script>

</body>
</html>

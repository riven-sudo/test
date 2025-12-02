<?php
include('../config/constants.php');

if (isset($_SESSION['reset_user_id'])) {
    $user_id = $_SESSION['reset_user_id'];

    if (isset($_POST['submit'])) {
        $user_id = $_POST['user_id']; // from hidden input
        $newPass = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $update = "UPDATE tbl_admin SET password='$newPass' WHERE id=$user_id";
        $resUpdate = mysqli_query($conn, $update);

        if ($resUpdate) {
            unset($_SESSION['reset_user_id']); // clear reset session
            echo "<div class='success'>Password updated! <a href='login.php'>Login</a></div>";
            exit();
        } else {
            echo "<div class='error'>Failed to update password. Try again.</div>";
        }
    }
} else {
    echo "<div class='error'>No reset session found. Please go back to Forgot Password.</div>";
    exit();
}
?>

<html>
<head><title>Reset Password</title></head>
<body>
    <h1>Reset Password</h1>
    <form method="POST">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="password" name="password" placeholder="New Password" required><br><br>
        <button type="submit" name="submit">Reset Password</button>
    </form>
</body>
</html>

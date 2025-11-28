<?php
include('../config/constants.php');

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $pin = mysqli_real_escape_string($conn, $_POST['pin']);

    // Check if user + PIN match
    $sql = "SELECT id FROM tbl_admin WHERE username='$username' AND pin='$pin' LIMIT 1";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $user = mysqli_fetch_assoc($res);
        $_SESSION['reset_user_id'] = $user['id'];

        // Redirect to reset password form
        header("Location: reset-password.php");
        exit();
    } else {
        echo "<div class='error'>Invalid username or PIN.</div>";
    }
}
?>

<html>
<head><title>Forgot Password (PIN)</title></head>
<body>
    <h1>Forgot Password</h1>
    <form method="POST">
        Username: <br>
        <input type="text" name="username" required><br><br>
        PIN (4 digits): <br>
        <input type="text" name="pin" maxlength="4" pattern="\d{4}" required><br><br>
        <button type="submit" name="submit">Verify</button>
    </form>
</body>
</html>


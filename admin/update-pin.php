<?php
include('../config/constants.php');
include('partials/admin-check.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header('location:'.SITEURL.'admin/manage-admin.php');
    exit();
}

if (isset($_POST['submit'])) {
    $pin = mysqli_real_escape_string($conn, $_POST['pin']);

    // Fetch old PIN for audit (do not store actual PIN)
    $old_row = null;
    $sel_old = mysqli_query($conn, "SELECT id, username FROM tbl_admin WHERE id=$id LIMIT 1");
    if ($sel_old && mysqli_num_rows($sel_old) == 1) $old_row = mysqli_fetch_assoc($sel_old);

    $sql = "UPDATE tbl_admin SET pin='$pin' WHERE id=$id";
    $res = mysqli_query($conn, $sql);

    if ($res) {
        $_SESSION['update'] = "<div class='success'>PIN updated successfully</div>";
        @Audit::log('update_admin_pin', 'tbl_admin', $id, null, array('pin'=>'***','username'=>$old_row['username'] ?? null));
    } else {
        $_SESSION['update'] = "<div class='error'>Failed to update PIN</div>";
        @Audit::log('update_admin_pin_failed', 'tbl_admin', $id, null, array('username'=>$old_row['username'] ?? null));
    }

    header('location:'.SITEURL.'admin/manage-admin.php');
    exit();
}
?>

<html>
<head>
    <title>Update PIN</title>
</head>
<body>
    <h1>Update PIN</h1>
    <form method="POST">
        <label>New PIN (4 digits)</label><br>
        <input type="text" name="pin" maxlength="4" pattern="\d{4}" required><br><br>
        <button type="submit" name="submit">Update PIN</button>
    </form>
</body>
</html>

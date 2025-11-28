<?php include('partials/menu.php'); ?>
<?php include('partials/admin-check.php'); ?>

<div class="main-content">
    <div class="wrapper">
        <h1>Add User</h1>
        <br><br>

        <?php
            if(isset($_SESSION['add'])) {
                echo $_SESSION['add'];
                unset($_SESSION['add']);
            }
        ?>
        <br><br>

        <form action="" method="POST">
            <table class="tbl-30">
                <tr>
                    <td>Full Name: </td>
                    <td>
                        <input type="text" name="full_name" placeholder="Enter full name" required>
                    </td>
                </tr>

                <tr>
                    <td>Username: </td>
                    <td>
                        <input type="text" name="username" placeholder="Enter username" required>
                    </td>
                </tr>

                <tr>
                    <td>Password: </td>
                    <td>
                        <input type="password" name="password" placeholder="Enter password" required>
                    </td>
                </tr>

                <tr>
                    <td>PIN (4 digits): </td>
                    <td>
                        <input type="text" name="pin" maxlength="4" pattern="\d{4}" placeholder="e.g. 1234" required>
                    </td>
                </tr>

                <tr>
                    <td>Role: </td>
                    <td>
                        <select name="role" required>
                            <option value="admin">Admin</option>
                            <option value="kitchen">Kitchen Staff</option>
                            <option value="delivery">Delivery Staff</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <input type="submit" name="submit" value="Add User" class="btn-secondary">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php include('partials/footer.php'); ?>

<?php
// Process form submission
if(isset($_POST['submit'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure hash
    $pin = mysqli_real_escape_string($conn, $_POST['pin']); // new PIN
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "INSERT INTO tbl_admin SET
        full_name='$full_name',
        username='$username',
        password='$password',
        pin='$pin',
        role='$role'
    ";

    $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    if($res) {
        $_SESSION['add'] = "<div class='success'>User Added Successfully</div>";
        header("location:".SITEURL.'admin/manage-admin.php');
        exit();
    } else {
        $_SESSION['add'] = "<div class='error'>Failed to Add User</div>";
        header("location:".SITEURL.'admin/add-admin.php');
        exit();
    }
}
?>

<?php include('partials/menu.php'); ?>
<?php include('partials/admin-check.php'); ?>

<div class="main-content">
    <div class="wrapper">
        <h1>Change Password</h1>
        <br><br>

        <?php
            if(isset($_GET['id']))
            {
                $id=$_GET['id'];
            }
        ?>

        <form action="" method="POST">

            <table class="tbl-30">
                <tr>
                    <td>Current Password: </td>
                    <td>
                        <input type="password" name="current_password" placeholder="Current Password">
                    </td>
                </tr>

                <tr>
                    <td>New Password: </td>
                    <td>
                        <input type="password" name="new_password" placeholder="New Password">
                    </td>
                </tr>

                <tr>
                    <td>Confirm Password: </td>
                    <td>
                        <input type="password" name="confirm_password" placeholder="Confirm Password">
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="submit" name="submit" value="Change Password" class="btn-secondary">
                    </td>
                </tr>

            </table>

        </form>

    </div>
</div>

<?php

            //Check whether submit button is clicked or not
            if(isset($_POST['submit']))
            {
                //echo "Clicked";

                //Get the data from form
                $_POST['id'];
                $current_password = md5($_POST['current_password']);
                $new_password = md5($_POST['new_password']);
                $confirm_password = md5($_POST['confirm_password']);


                //Check whether the user with current ID and Password Exists or not
                $sql = "SELECT * FROM tbl_admin WHERE id=$id AND password= '$current_password'";

                //Execute the Query
                $res = mysqli_query($conn, $sql);

                if($res==true)
                {
                    //Check whether data is available or not
                    $count=mysqli_num_rows($res);

                    if($count==1)
                    {
                        //User Exists and Password can be changed 
                        //echo "User found";

                        //Check whether the new password and confirm match or not
                        if($new_password==$confirm_password)
                        {
                            //Update the password
                            $sql2 = "UPDATE tbl_admin SET
                                password='$new_password'
                                WHERE id=$id
                            ";

                            //Execute the Query
                            $res2 = mysqli_query($conn, $sql2);

                            //Check whether the query executed or not
                            if($res2==true)
                            {
                                //Display Success Message
                                //Redirect to Manange admin page with Success message
                                $_SESSION['change-pwd'] = "<div class='success'>Password Changed Successfully</div>";
                                //Redirect the user
                                header('location:'.SITEURL.'admin/manage-admin.php');
                            }
                            else
                            {
                                //Display Error Message
                                $_SESSION['change-pwd'] = "<div class='error'>Failed to Change Password</div>";
                                //Redirect the user
                                header('location:'.SITEURL.'admin/manage-admin.php');
                            }


                        }
                        else
                        {
                            //Redirect to Manange admin page with error message
                            $_SESSION['pwd-not-match'] = "<div class='error'>Password did not Match</div>";
                            //Redirect the user
                            header('location:'.SITEURL.'admin/manage-admin.php');
                        }
                    }
                    else
                    {
                        //User does not Exists set message and redirect
                        $_SESSION['user-not-found'] = "<div class='error'>User not found</div>";
                        //Redirect the user
                        header('location:'.SITEURL.'admin/manage-admin.php');
                    }
                }

                //Check whether the New Password and Confirm Password match or not
 
                //Change Password if all is true
            }

?>

<?php include('partials/footer.php'); ?>
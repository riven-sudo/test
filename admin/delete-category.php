<?php
    //Include constants file
    include('../config/constants.php');
    include('partials/admin-check.php'); 

    //echo "Delete page";
    //Check whether the id and image_name value is set or not
    if(isset($_GET['id']) AND isset($_GET['image_name']))
    {
        //Get the value and delete
        //echo "Get value and Delete";
        $id = $_GET['id'];
        $image_name = $_GET['image_name'];

        //Remove the physical image file if available
        if($image_name != "")
        {
            //Image is Available
            $path = "../images/category/".$image_name;
            //Remove the image
            $remove = unlink($path);

            //If fail to remove image then add an error message and stop the process
            if($remove==false)
            {
                //Set the session message
                $_SESSION['remove'] = "<div class='error'>Failed to Remove Category Image</div>";
                //Redirect to manage category page
                header('location:'.SITEURL.'admin/manage-category.php');
                //Stop the process
                die();
            }
        }

        // Fetch the existing row for audit
        $old_row = null;
        $sel_old = mysqli_query($conn, "SELECT * FROM tbl_category WHERE id=$id LIMIT 1");
        if ($sel_old && mysqli_num_rows($sel_old) == 1) {
            $old_row = mysqli_fetch_assoc($sel_old);
        }

        //Delete data from database 
        //SQL Query delete data from databse
        $sql = "DELETE FROM tbl_category WHERE id=$id";

        //Execute the Query
        $res = mysqli_query($conn, $sql);

        //Check whether the data is delete form database or not
        if($res==true)
        {
            //Set Success message and redirect
            $_SESSION['delete'] = "<div class='success'>Category Deleted Successfully</div>";

            @Audit::log('delete_category', 'tbl_category', $id, $old_row, null);

            //Redirect to manage category
            header('location:'.SITEURL.'admin/manage-category.php');
        }
        else
        {
            //Set fail message and redirect
            $_SESSION['delete'] = "<div class='error'>Failed To Delete Category.</div>";
            @Audit::log('delete_category_failed', 'tbl_category', $id, $old_row, null);
            //Redirect to manage category
            header('location:'.SITEURL.'admin/manage-category.php');
        }


    }
    else
    {
        //Redirect to manage category page
        header('location:'.SITEURL.'admin/manage-category.php');
    }

?>
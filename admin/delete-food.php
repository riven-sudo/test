<?php
    //include constants page
    include('../config/constants.php');
    include('partials/admin-check.php'); 

    //echo "Delete";
    if(isset($_GET['id']) && isset($_GET['image_name'])) 
    {
        //Proceed to Delete
        //echo "Process to Delete";

        //Get ID and image name
        $id = $_GET['id'];
        $image_name = $_GET['image_name'];

        //Remove the image if available
        //Check whether the image is available or not and delete only if available
        if($image_name != "")
        {
            //It has image and need to remove folder
            //Get the image path
            $path = "../images/food/".$image_name;

            //Remove image file from folder
            $remove = unlink($path);

            //Check whether the image is remove or not
            if($remove==false)
            {
                //Failed to remove image
                $_SESSION['upload'] = "<div class='error'>Failed to remove image file</div>";

                //Redirect to manage food 
                header('location:'.SITEURL.'admin/manage-food.php');

                //Stop the process of deleting food
                die();
            }
        }

        //Delete Food from database
        $sql = "DELETE FROM tbl_food WHERE id=$id";

        //Execute the Query
        $res = mysqli_query($conn, $sql);

        //Check whether the Query is Executed or not and set the session message respectively
        //Redirect to Manage food with Session Message
        if($res==true)
        {
            //Food Deleted
            $_SESSION['delete'] = "<div class='success'>Food Deleted Successfully</div>";
            header('location:'.SITEURL.'admin/manage-food.php');
        }
        else
        {
            //Failed to Delete Food
            $_SESSION['delete'] = "<div class='error'>Failed to Delete Food</div>";
            header('location:'.SITEURL.'admin/manage-food.php');
        }

        //Redirect to Manage food with Session Message
    }
    else
    {
        //Redirect to Manage Food Page
        //echo "Redirect";
        $_SESSION['unauthorize'] = "<div class='error'>Unauthorize Access</div>";
        header('location:'.SITEURL.'admin/manage-food.php');
    }

?>
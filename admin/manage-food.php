<?php include('partials/menu.php') ?>
<?php include('partials/admin-check.php'); ?>

<div class="main-content">
    <div class="wrapper">
        <h1>Manage Food</h1>

        <br /><br />

                <!--Button to Add Admin-->
                <a href="<?php echo SITEURL; ?>admin/add-food.php" class="btn-primary">Add Food</a>

                <br /><br /><br />

                <?php
                
                    if(isset($_SESSION['add']))
                    {
                        echo $_SESSION['add'];
                        unset($_SESSION['add']);
                    }

                    if(isset($_SESSION['delete']))
                    {
                        echo $_SESSION['delete'];
                        unset($_SESSION['delete']);
                    }

                    if(isset($_SESSION['upload']))
                    {
                        echo $_SESSION['upload'];
                        unset($_SESSION['upload']);
                    }

                    if(isset($_SESSION['unauthorize']))
                    {
                        echo $_SESSION['unauthorize'];
                        unset($_SESSION['unauthorize']);
                    }

                    if(isset($_SESSION['update']))
                    {
                        echo $_SESSION['update'];
                        unset($_SESSION['update']);
                    }


                ?>

                <div class="table-responsive">
                <table class="tbl-full">
                    <tr>
                        <th>S.N</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Featured</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>

                    <?php
                    
                        //Create a SQL Query to get all the Food
                        $sql = "SELECT * FROM tbl_food";

                        //Execute the Query
                        $res = mysqli_query($conn, $sql);

                        //Count rows to check whether we have food or not
                        $count = mysqli_num_rows($res);

                        //Create serial number variable and set default value as 1
                        $sn=1;

                        if($count>0)
                        {
                            //We have food in database
                            //Get the food from Database and display
                            while($row=mysqli_fetch_assoc($res))
                            {
                                //Get the value from individual columns
                                $id = $row['id'];
                                $title = $row['title'];
                                $price = $row['price'];
                                $image_name = $row['image_name'];
                                $featured = $row['featured'];
                                $active = $row['active'];
                                ?>

                                <tr>
                                    <td data-label="S.N"><?php echo $sn++; ?>. </td>
                                    <td data-label="Title"><?php echo $title; ?></td>
                                    <td data-label="Price">$<?php echo $price; ?></td>
                                    <td data-label="Image">
                                        <?php
                                            //Check whether we have image or not
                                            if($image_name=="")
                                            {
                                                //We dont have image, display error message
                                                echo "<div class='error'>Image not Added</div>";
                                            }
                                            else
                                            {
                                                //We have image, Display image
                                                ?>
                                                <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo htmlspecialchars($title); ?>">
                                                <?php
                                            }
                                        ?>
                                    </td>
                                    <td data-label="Featured"><?php echo $featured; ?></td>
                                    <td data-label="Active"><?php echo $active; ?></td>
                                    <td data-label="Actions">
                                        <a title="Edit Food" href="<?php echo SITEURL; ?>admin/update-food.php?id=<?php echo $id; ?>" class="action-btn btn-edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a title="Delete Food" href="<?php echo SITEURL; ?>admin/delete-food.php?id=<?php echo $id; ?>&image_name=<?php echo $image_name; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this food item?');"><i class="fa-solid fa-trash"></i></a>
                                        <a title="Reset Ratings" href="reset-ratings.php?food_id=<?php echo $id; ?>" class="action-btn btn-secondary"><i class="fa-solid fa-rotate"></i></a>

                                    </td>
                                </tr>

                                <?php
                            }
                        }
                        else
                        {
                            //Food not added in database
                            echo "<tr> <td colspan='7'class='error'> Food not Added Yet </td> </tr>";
                        }
                    
                    ?>

                </table>

    </div>
</div>

<?php include('partials/footer.php') ?>
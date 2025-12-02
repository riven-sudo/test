<?php include('partials/menu.php') ?>
<?php include('partials/admin-check.php'); ?>

<div class="main-content">
    <div class="wrapper">
        <h1>Manage Category</h1>

        <br /><br />

        <?php
        
            if(isset($_SESSION['add']))
            {
                echo $_SESSION['add'];
                unset($_SESSION['add']);
            }

            if(isset($_SESSION['remove']))
            {
                echo $_SESSION['remove'];
                unset($_SESSION['remove']);
            }

            if(isset($_SESSION['delete']))
            {
                echo $_SESSION['delete'];
                unset ($_SESSION['delete']);
            }

            if(isset($_SESSION['no-category-found']))
            {
                echo $_SESSION['no-category-found'];
                unset($_SESSION['no-category-found']);
            }

            if(isset($_SESSION['update']))
            {
                echo $_SESSION['update'];
                unset($_SESSION['update']);
            }

            if(isset($_SESSION['upload']))
            {
                echo $_SESSION['upload'];
                unset($_SESSION['upload']);
            }

            if(isset($_SESSION['failed-remove']))
            {
                echo $_SESSION['failed-remove'];
                unset($_SESSION['failed-remove']);
            }
        
        ?>

        <br><br>

                <!--Button to Add Admin-->
                <a href="<?php echo SITEURL; ?>admin/add-category.php" class="btn-primary">Add Category</a>

                <br /><br /><br />

                <div class="table-responsive">
                <table class="tbl-full">
                    <tr>
                        <th>S.N</th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Featured</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>

                    <?php

                        //Query to get all Category from database
                        $sql = "SELECT * FROM tbl_category";

                        //Execute Query
                        $res = mysqli_query($conn, $sql);

                        //Count Rows
                        $count = mysqli_num_rows($res);

                        //Create a serial number variable and assign value as 1 
                        $sn=1;


                        //Check whether we have data in database or not
                        if($count>0)
                        {
                            //We have data in Database
                            //Get the data and display
                            while($row=mysqli_fetch_assoc($res))
                            {
                                $id = $row['id'];
                                $title = $row['title'];
                                $image_name = $row['image_name'];
                                $featured = $row['featured'];
                                $active = $row['active'];

                                ?>

                                    <tr>
                                        <td data-label="S.N"><?php echo $sn++ ?>. </td>
                                        <td data-label="Title"><?php echo $title; ?></td>

                                        <td data-label="Image">
                                            <?php
                                                //Check whether image name is available or not
                                                if($image_name!="")
                                                {
                                                    //Display the image 
                                            ?>
                                                    <img src="<?php echo SITEURL; ?>images/category/<?php echo $image_name; ?>" alt="<?php echo htmlspecialchars($title); ?>" >
                                            <?php
                                                }
                                                else
                                                {
                                                    //Display the message
                                                    echo "<div class='error'>Image not added</div>";
                                                }
                                            ?>
                                        </td>

                                        <td data-label="Featured"><?php echo $featured; ?></td>
                                        <td data-label="Active"><?php echo $active; ?></td>
                                        <td data-label="Actions">
                                            <a title="Edit Category" href="<?php echo SITEURL; ?>admin/update-category.php?id=<?php echo $id; ?>" class="action-btn btn-edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a title="Delete Category" href="<?php echo SITEURL; ?>admin/delete-category.php?id=<?php echo $id; ?>&image_name=<?php echo $image_name; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this category?');"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php

                            }
                        }
                        else
                        {
                            //We dont have data
                            //well display the message inside table
                            ?>

                            <tr>
                                <td colspan="6"><div class="error">No Category Added.</div></td>
                            </tr>

                            <?php
                        }

                    ?>

                    
                </table>

    </div>
</div>

<?php include('partials/footer.php') ?>
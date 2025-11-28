<?php include('partials-front/menu.php'); ?>

<?php
    //Check whether food id is set or not
    if(isset($_GET['food_id']))
    {
        $food_id = $_GET['food_id']; 

        //Get the details of the selected food
        $sql = "SELECT * FROM tbl_food WHERE id=$food_id";
        $res = mysqli_query($conn, $sql);
        $count = mysqli_num_rows($res);

        if($count==1)
        {
            $row = mysqli_fetch_assoc($res);
            $title = $row['title'];
            $price = $row['price'];
            $image_name = $row['image_name'];
        }
        else
        {
            header('location:'.SITEURL);
        }
    }
    else
    {
        header('location:'.SITEURL);
    }
?>

<section class="food-search">
    <div class="container">
        
        <h2 class="text-center text-white">Dine in - Fill this form to confirm your order.</h2>

        <form action="" method="POST" class="order">
            <fieldset>
                <legend>Selected Food</legend>

                <div class="food-menu-img">
                    <?php
                        if($image_name=="")
                        {
                            echo "<div class='error'>Image not available</div>";
                        } 
                        else
                        {
                            ?>
                            <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" 
                                 alt="<?php echo $title; ?>" class="img-responsive img-curve">
                            <?php
                        }
                    ?>
                </div>

                <div class="food-menu-desc">
                    <h3><?php echo $title; ?></h3>
                    <input type="hidden" name="food" value="<?php echo $title; ?>">

                    <p class="food-price">$<?php echo $price; ?></p>
                    <input type="hidden" name="price" value="<?php echo $price; ?>">

                    <div class="order-label">Quantity</div>
                    <input type="number" name="qty" class="input-responsive" value="1" required>
                </div>
            </fieldset>
            
            <fieldset>
                <legend>Customer Details</legend>

                <div class="order-label">Full Name</div>
                <input type="text" name="full-name" class="input-responsive" required>

                <div class="order-label">Table Number</div>
                <input type="text" name="table-number" class="input-responsive" required>

                <div class="order-label">Select Payment Method</div>
                <label>
                    <input type="radio" name="payment" value="Cash" checked> Cash
                </label>
                <label>
                    <input type="radio" name="payment" value="GCash"> GCash
                </label>
                <br><br>

                <input type="submit" name="submit" value="Confirm Order" class="btn btn-primary">
            </fieldset>
        </form>

        <?php
            if(isset($_POST['submit']))
            {
                $food = $_POST['food'];
                $price = $_POST['price'];
                $qty = $_POST['qty'];
                $total = $price * $qty;
                $order_date = date("Y-m-d h:i:s");
                $status = "Dine-in";

                $customer_name = $_POST['full-name'];
                $table_number = $_POST['table-number'];
                $payment_method = $_POST['payment'];

                // Save the order in database
                $sql2 = "INSERT INTO tbl_order SET
                    food = '$food',
                    price = $price,
                    qty = $qty,
                    total = $total,
                    order_date = '$order_date',
                    status = '$status',
                    customer_name = '$customer_name',
                    customer_contact = '$table_number',
                    payment_method = '$payment_method'
                ";

                $res2 = mysqli_query($conn, $sql2);

                if($res2==true)
                {
                    $_SESSION['order'] = "<div class='success text-center'>Food Ordered Successfully</div>";
                    header('location:'.SITEURL);
                }
                else
                {
                    $_SESSION['order'] = "<div class='error text-center'>Failed to Order Food</div>";
                    header('location:'.SITEURL);
                }
            }
        ?>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>

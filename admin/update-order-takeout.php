<?php include('partials/menu.php'); ?>

<div class="main-content">
    <div class="wrapper">
        <h1>Update Order takeout</h1>
        <br><br>

        <?php
        
            //Check whether id is set or not
            if(isset($_GET['id']))
            {
                //Get the order details
                $id=$_GET['id'];

                //Get all the details based on this id
                //SQL Query to get the order details
                $sql = "SELECT * FROM tbl_takeout WHERE id=$id";
                //Execute Query
                $res = mysqli_query($conn, $sql);
                //Count rows 
                $count = mysqli_num_rows($res);

                if($count==1)
                {
                    //Detail available
                    $row=mysqli_fetch_assoc($res);

                    $food = $row['food'];
                    $price = $row['price'];
                    $qty = $row['qty'];
                    $status = $row['status'];
                    $customer_name = $row['customer_name'];
                    $customer_contact = $row['customer_contact'];
                    $customer_email = $row['customer_email'];
                    $customer_address = $row['customer_address'];
                    
                    $payment_method = $row['payment_method'];
                   
                }
                else
                {
                    //Detail not available
                    //redirect to manage order
                    header('location:'.SITEURL.'admin/manage-order-takeout.php');
                }
            }
            else
            {
                //Redirect to manage order page
                header('location:'.SITEURL.'admin/manage-order-takeout.php');
            }
        
        ?>

        <form action="" method="POST">

<table class="tbl-30">


    <tr>
        <td>Name: </td>
        <td>
            <input type="text" name="customer_name" value="<?php echo $customer_name; ?>">
        </td>
    </tr>

    <tr>
        <td>Phone Number: </td>
        <td>
            <input type="text" name="customer_contact" value="<?php echo $customer_contact; ?>">
        </td>
    </tr>

      <tr>
        <td>Email: </td>
        <td>
            <input type="text" name="customer_email" value="<?php echo $customer_email; ?>">
        </td>
    </tr>

      <tr>
        <td>Address: </td>
        <td>
            <input type="text" name="customer_address" value="<?php echo $customer_address; ?>">
        </td>
    </tr>

    <tr>
        <td>Food Name</td>
        <td>
            <input type="text" name="food" value="<?php echo $food; ?>">
        </td>
    </tr>

    <tr>
        <td>Price</td>
        <td>
            <input type="number" name="price" value="<?php echo $price; ?>" step="0.01">
        </td>
    </tr>

    <tr>
        <td>Qty</td>
        <td>
            <input type="number" name="qty" value="<?php echo $qty; ?>">
        </td>
    </tr>

    <tr>
        <td>Total</td>
        <td>
            <b><?php echo $price * $qty; ?></b>
        </td>
    </tr>

    <tr>
        <td>Status</td>
        <td>
            <select name="status">
                <option <?php if($status=="Preparing"){echo "selected";} ?> value="Ordered">Preparing</option>
                <option <?php if($status=="On Delivery"){echo "selected";} ?> value="On Delivery">On Delivery</option>
                <option <?php if($status=="Delivered"){echo "selected";} ?> value="Delivered">Delivered</option>
                
               
            </select>
        </td>
    </tr>


    <tr>
        <td>Payment: </td>
        <td>
            <select name="payment">
                <option value="Cash" <?php if(isset($row['payment']) && $row['payment']=="Cash") echo "selected"; ?>>Cash</option>
                <option value="GCash" <?php if(isset($row['payment']) && $row['payment']=="GCash") echo "selected"; ?>>GCash</option>
            </select>
        </td>
    </tr>

    

   

    <tr>
        <td colspan="2">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="submit" name="submit" value="Update" class="btn-secondary">
        </td>
    </tr>
</table>


        </form>

        <?php
            //Check whethe update button is clicked or not
            if(isset($_POST['submit']))
{
    $id = $_POST['id'];
    $food = $_POST['food'];
    $price = $_POST['price'];
    $qty = $_POST['qty'];
    $total = $price * $qty;

    $status = $_POST['status'];
    $customer_name = $_POST['customer_name'];
    $customer_contact  = $_POST['customer_contact'];
    $customer_email  = $_POST['customer_email'];
    $customer_address  = $_POST['customer_address'];
    

   
   
    $payment_method = $_POST['payment'];


    $sql2 = "UPDATE tbl_takeout SET
        food = '$food',
        price = $price,
        qty = $qty,
        total = $total,
        status = '$status',
        customer_name = '$customer_name',
        customer_contact = '$customer_contact',
        customer_email = '$customer_email',
        customer_address = '$customer_address',

        
        payment_method = '$payment_method'
        WHERE id=$id
    ";

    $res2 = mysqli_query($conn, $sql2);

    if($res2==true)
    {
        $_SESSION['update'] = "<div class='success'>Order Updated Successfully</div>";
        header('location:'.SITEURL.'admin/manage-order-takeout.php');
    }
    else
    {
        $_SESSION['update'] = "<div class='error'>Failed to Update Order</div>";
        header('location:'.SITEURL.'admin/manage-order-takeout.php');
    }
}

        ?>

    </div>
</div>

<?php include('partials/footer.php'); ?>
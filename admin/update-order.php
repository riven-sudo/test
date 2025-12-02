<?php 
include('partials/menu.php'); 

// --- Check if ID is set
if (!isset($_GET['id'])) {
    header('location:' . SITEURL . 'admin/manage-order-dinein.php');
    exit();
}

$id = (int) $_GET['id'];

// --- Fetch order details
$sql = "SELECT * FROM tbl_order WHERE id=$id LIMIT 1";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) != 1) {
    header('location:' . SITEURL . 'admin/manage-order-dinein.php');
    exit();
}

$row = mysqli_fetch_assoc($res);

$food          = $row['food'];
$price         = $row['price'];
$qty           = $row['qty'];
$status        = $row['status'];
$customer_name = $row['customer_name'];
$payment_method= $row['payment_method'];

// --- Default total
$total = $price * $qty;

// --- Check if customer is logged in & member
$discount = 0;
if (isset($_SESSION['customer_id'])) {
    $customer_id = (int) $_SESSION['customer_id'];

    $customer_sql = "SELECT is_member, discount FROM tbl_customer WHERE id=$customer_id LIMIT 1";
    $customer_res = mysqli_query($conn, $customer_sql);

    if ($customer_res && mysqli_num_rows($customer_res) == 1) {
        $customer = mysqli_fetch_assoc($customer_res);

        if ($customer['is_member'] == 1) {
            $discount = (float) $customer['discount']; // e.g., 5
            $total = $total - ($total * $discount / 100);
        }
    }
}

// --- Handle update
if (isset($_POST['submit'])) {
    $food          = mysqli_real_escape_string($conn, $_POST['food']);
    $price         = (float)$_POST['price'];
    $qty           = (int)$_POST['qty'];
    $status        = mysqli_real_escape_string($conn, $_POST['status']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $payment_method= mysqli_real_escape_string($conn, $_POST['payment']);

    // Recalculate total
    $total = $price * $qty;
    if ($discount > 0) {
        $total = $total - ($total * $discount / 100);
    }

    $sql2 = "UPDATE tbl_order SET
        food = '$food',
        price = $price,
        qty = $qty,
        total = $total,
        status = '$status',
        customer_name = '$customer_name',
        payment_method = '$payment_method'
        WHERE id=$id
    ";

    $res2 = mysqli_query($conn, $sql2);

    if ($res2) {
        $_SESSION['update'] = "<div class='success'>Order Updated Successfully".($discount > 0 ? " (Member Discount Applied: {$discount}%)" : "")."</div>";
    } else {
        $_SESSION['update'] = "<div class='error'>Failed to Update Order</div>";
    }

    header('location:' . SITEURL . 'admin/manage-order-dinein.php');
    exit();
}
?>

<div class="main-content">
    <div class="wrapper">
        <h1>Update Order</h1>
        <form action="" method="POST">
            <table class="tbl-30">
                <tr>
                    <td>Name: </td>
                    <td><input type="text" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>"></td>
                </tr>
                <tr>
                    <td>Food Name</td>
                    <td><input type="text" name="food" value="<?php echo htmlspecialchars($food); ?>"></td>
                </tr>
                <tr>
                    <td>Price</td>
                    <td><input type="number" name="price" value="<?php echo $price; ?>" step="0.01"></td>
                </tr>
                <tr>
                    <td>Qty</td>
                    <td><input type="number" name="qty" value="<?php echo $qty; ?>"></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td>
                        <b>₱<?php echo number_format($total, 2); ?><?php if ($discount > 0) echo " (Member Discount Applied)"; ?></b>
                    </td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>
                        <select name="status">
                            <option value="Preparing" <?php if ($status=="Preparing") echo "selected"; ?>>Preparing</option>
                            <option value="Take-out" <?php if ($status=="Take-out") echo "selected"; ?>>Take-out</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Payment: </td>
                    <td>
                        <select name="payment">
                            <option value="Cash" <?php if ($payment_method=="Cash") echo "selected"; ?>>Cash</option>
                            <option value="GCash" <?php if ($payment_method=="GCash") echo "selected"; ?>>GCash</option>
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
    </div>
</div>

<?php include('partials/footer.php'); ?>

<?php
include('partials-front/menu.php');
include_once(__DIR__ . '/config/constants.php');


// Make sure customer is logged in
$customer_id = $_SESSION['customer_id'] ?? 0; 

// Fetch member info
$discount = 0;
if($customer_id){
    $sql_member = "SELECT is_member, discount FROM tbl_customer WHERE id=$customer_id";
    $res_member = mysqli_query($conn, $sql_member);
    if($res_member && mysqli_num_rows($res_member) == 1){
        $member = mysqli_fetch_assoc($res_member);
        if($member['is_member'] == 1){
            $discount = $member['discount']; // 5%
        }
    }
}

// ✅ Handle order submission from cart_view.php
if (isset($_POST['submit_order'])) {
    $customer_name   = mysqli_real_escape_string($conn, $_POST['name']);
    $order_type      = mysqli_real_escape_string($conn, $_POST['order_type'] ?? "Take-out"); // dine-in or takeout
    $payment_method  = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $transaction_number = uniqid("TXN"); // unique transaction number

    // Server-side: if payment is GCash, ensure proof image was uploaded and is valid
    if (strtolower($payment_method) === 'gcash') {
        if (!isset($_FILES['gcash_proof']) || $_FILES['gcash_proof']['error'] !== UPLOAD_ERR_OK) {
            echo "<script>alert('GCash selected — please upload a proof image.'); window.location.href='cart-view.php';</script>";
            exit();
        }

        // Validate image
        $tmpPath = $_FILES['gcash_proof']['tmp_name'];
        $fileInfo = @getimagesize($tmpPath);
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($fileInfo === false || $_FILES['gcash_proof']['size'] > $maxSize) {
            echo "<script>alert('Uploaded file is not a valid image or is too large (max 5MB).'); window.location.href='cart-view.php';</script>";
            exit();
        }

        // Move uploaded file to uploads/gcash/
        $uploadsDir = __DIR__ . '/uploads/gcash/';
        if (!is_dir($uploadsDir)) {
            @mkdir($uploadsDir, 0755, true);
        }
        $origName = basename($_FILES['gcash_proof']['name']);
        $ext = pathinfo($origName, PATHINFO_EXTENSION);
        $newName = uniqid('gcash_') . '.' . $ext;
        $dest = $uploadsDir . $newName;
        if (!move_uploaded_file($tmpPath, $dest)) {
            echo "<script>alert('Failed to save uploaded proof image. Please try again.'); window.location.href='cart-view.php';</script>";
            exit();
        }
        // Note: not stored in DB here — can be saved to DB if a column exists.
    }

    if (!empty($_SESSION['cart']) && $order_type === "Take-out") { 
        $total_order = 0; // store total for the entire order
        foreach ($_SESSION['cart'] as $item) {
            $food  = mysqli_real_escape_string($conn, $item['title']);
            $price = (float) $item['price'];
            $qty   = (int) $item['qty'];
            $total = $price * $qty;

            // Apply member discount
            if($discount > 0){
                $total = $total - ($total * $discount / 100);
            }

            $total_order += $total; // sum total for order

            // For Take-out orders there is no table number — use 0 as a sentinel value
            $table_number = 0;

            $sql_insert = "INSERT INTO tbl_order 
                (transaction_number, food, price, qty, total, order_date, status, customer_name, payment_method, order_type, table_number, customer_id) 
                VALUES 
                ('$transaction_number', '$food', $price, $qty, $total, NOW(), 'Preparing', '$customer_name', '$payment_method', 'Take-out', $table_number, $customer_id)";

            mysqli_query($conn, $sql_insert);
            $inserted_id = mysqli_insert_id($conn);
            @Audit::log('create_order', 'tbl_order', $inserted_id, null, array('transaction_number'=>$transaction_number,'food'=>$food,'qty'=>$qty,'price'=>$price,'total'=>$total,'customer_name'=>$customer_name,'payment_method'=>$payment_method));
        }

        // clear cart after saving
        unset($_SESSION['cart']);

        // Redirect user back to main menu (index) with a small JS alert to confirm
        $msg = ($discount > 0)
            ? "Take-out Order placed! You got {$discount}% member discount applied. Total: ₱".number_format($total_order,2)
            : "Take-out Order successfully placed. Total: ₱".number_format($total_order,2);
        // Use JS redirect because headers may already be sent by included partials
        echo "<script>alert(" . json_encode($msg) . "); window.location.href='index.php';</script>";
        exit();
    }
}

// ✅ Fetch only Take-out orders grouped by transaction
$orders = [];
$sql_orders = "SELECT * FROM tbl_order WHERE order_type = 'Take-out' ORDER BY order_date DESC";
$res_orders = mysqli_query($conn, $sql_orders);
if ($res_orders && mysqli_num_rows($res_orders) > 0) {
    while ($row = mysqli_fetch_assoc($res_orders)) {
        $orders[$row['transaction_number']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Take-Out Orders</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align:center; }
        th { background: #f4f4f4; }
        .collapsible { cursor: pointer; padding: 10px; border: none; text-align: left; background: #f1f1f1; width: 100%; font-weight:bold; }
        .content { display: none; padding: 10px; background: #fafafa; }
        .success { color: #2ed573; font-weight:bold; }
    </style>
    <script>
        function toggleCollapse(id) {
            let content = document.getElementById(id);
            content.style.display = content.style.display === "block" ? "none" : "block";
        }
    </script>
</head>
<body>

<h1>📋 Take-out Orders</h1>

<?php if (empty($orders)): ?>
    <p>No Take-out orders found.</p>
<?php else: ?>
    <?php foreach ($orders as $txn => $items): ?>
        <?php $total_amount = array_sum(array_column($items, 'total')); ?>
        <button class="collapsible" onclick="toggleCollapse('content_<?php echo $txn; ?>')">
            ▶ <?php echo $txn; ?> - 
            <?php echo htmlspecialchars($items[0]['customer_name']); ?> | 
            ₱<?php echo number_format($total_amount, 2); ?>
        </button>
        <div class="content" id="content_<?php echo $txn; ?>">
            <table>
                <tr>
                    <th>Food</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($items as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['food']); ?></td>
                    <td><?php echo number_format($order['price'], 2); ?></td>
                    <td><?php echo $order['qty']; ?></td>
                    <td><?php echo number_format($order['total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>

<?php include('partials-front/footer.php'); ?>

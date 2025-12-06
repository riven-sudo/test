<?php
include('partials-front/menu.php');
include_once(__DIR__ . '/config/constants.php');

// ✅ Make sure customer is logged in
$customer_id = $_SESSION['customer_id'] ?? 0;

// ✅ Fetch member info (for discount)
$discount = 0;
if ($customer_id) {
    $sql_member = "SELECT is_member, discount FROM tbl_customer WHERE id=$customer_id";
    $res_member = mysqli_query($conn, $sql_member);
    if ($res_member && mysqli_num_rows($res_member) == 1) {
        $member = mysqli_fetch_assoc($res_member);
        if ($member['is_member'] == 1) {
            $discount = $member['discount']; // e.g. 5%
        }
    }
}

// ✅ Handle takeout order submission
if (isset($_POST['submit_order'])) {
    $customer_name    = mysqli_real_escape_string($conn, $_POST['name']);
    $customer_contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $customer_address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method   = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $transaction_number = uniqid("TXN");

    if (!empty($_SESSION['cart'])) {
        $total_order = 0;

        foreach ($_SESSION['cart'] as $item) {
            $food  = mysqli_real_escape_string($conn, $item['title']);
            $price = (float) $item['price'];
            $qty   = (int) $item['qty'];
            $total = $price * $qty;

            // ✅ Apply discount if member
            if ($discount > 0) {
                $total = $total - ($total * $discount / 100);
            }

            $total_order += $total;

            $sql_insert = "INSERT INTO tbl_takeout 
                (transaction_number, food, price, qty, total, order_date, status, customer_name, customer_contact, customer_address, payment_method, order_type) 
                VALUES 
                ('$transaction_number', '$food', $price, $qty, $total, NOW(), 'Preparing', '$customer_name', '$customer_contact', '$customer_address', '$payment_method', 'Takeout')";
            mysqli_query($conn, $sql_insert);
        }

        // ✅ Clear cart
        unset($_SESSION['cart']);

        // ✅ Success message
        if ($discount > 0) {
            echo "<p style='color:green;'>✅ Takeout Order placed! You got {$discount}% member discount applied. Total: ₱".number_format($total_order,2)."</p>";
        } else {
            echo "<p style='color:green;'>✅ Takeout Order successfully placed. Total: ₱".number_format($total_order,2)."</p>";
        }
    }
}

// ✅ Fetch existing takeout orders grouped by transaction
$orders = [];
$sql_orders = "SELECT * FROM tbl_takeout ORDER BY order_date DESC";
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
    <title>Takeout Orders</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align:center; }
        th { background: #f4f4f4; }
        .collapsible { cursor: pointer; padding: 10px; border: none; text-align: left; background: #f1f1f1; width: 100%; font-weight:bold; }
        .content { display: none; padding: 10px; background: #fafafa; }
    </style>
    <script>
        function toggleCollapse(id) {
            let content = document.getElementById(id);
            content.style.display = content.style.display === "block" ? "none" : "block";
        }
    </script>
</head>
<body>

<h1>📦 Delivery Orders</h1>
<button onclick="window.location.href='Multiple-orders.php'">Go to Take-outs</button>

<?php if (empty($orders)): ?>
    <p>No Delivery orders found.</p>
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
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Payment</th>
                </tr>
                <?php foreach ($items as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['food']); ?></td>
                    <td><?php echo number_format($order['price'], 2); ?></td>
                    <td><?php echo $order['qty']; ?></td>
                    <td><?php echo number_format($order['total'], 2); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_contact']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_address']); ?></td>
                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>

<?php include('partials-front/footer.php'); ?>

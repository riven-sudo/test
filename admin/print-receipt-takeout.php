<?php
include('../config/constants.php');
 include('partials/admin-check.php'); 

if (!isset($_GET['txn'])) {
    die("Transaction not specified.");
}

$txn = mysqli_real_escape_string($conn, $_GET['txn']);

// Fetch all orders for this transaction
$sql = "SELECT * FROM tbl_takeout WHERE transaction_number='$txn'";
$res = mysqli_query($conn, $sql);

$orders = [];
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $orders[] = $row;
    }
} else {
    die("No orders found for this transaction.");
}

$total_amount     = array_sum(array_column($orders, 'total'));
$customer_name    = $orders[0]['customer_name'];
$customer_contact = $orders[0]['customer_contact'];
$customer_email   = $orders[0]['customer_email'];
$customer_address = $orders[0]['customer_address'];
$payment_method   = $orders[0]['payment_method'];
$order_date       = $orders[0]['order_date'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Takeout Receipt - <?php echo $txn; ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt { width: 320px; margin: auto; }
        .receipt h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 4px; border-bottom: 1px solid #ddd; font-size: 14px; }
        .total { font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <h2>Takeout Receipt</h2>
        <p>
            <strong>Transaction:</strong> <?php echo $txn; ?><br>
            <strong>Customer:</strong> <?php echo htmlspecialchars($customer_name); ?><br>
            <strong>Phone:</strong> <?php echo htmlspecialchars($customer_contact); ?><br>
            <strong>Email:</strong> <?php echo htmlspecialchars($customer_email); ?><br>
            <strong>Address:</strong> <?php echo htmlspecialchars($customer_address); ?><br>
            <strong>Date:</strong> <?php echo $order_date; ?><br>
            <strong>Payment:</strong> <?php echo htmlspecialchars($payment_method); ?>
        </p>

        <table>
            <tr>
                <th>Food</th>
                <th>Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['food']); ?></td>
                <td><?php echo $order['qty']; ?></td>
                <td class="text-right">₱<?php echo number_format($order['price'], 2); ?></td>
                <td class="text-right">₱<?php echo number_format($order['total'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total">
                <td colspan="3" class="text-right">Grand Total:</td>
                <td class="text-right">₱<?php echo number_format($total_amount, 2); ?></td>
            </tr>
        </table>
        <p style="text-align:center;">Thank you for your order!</p>
    </div>
</body>
</html>

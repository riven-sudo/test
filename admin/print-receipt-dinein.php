<?php
include('../config/constants.php'); // adjust path if needed

if (!isset($_GET['txn'])) {
    die("Transaction not specified.");
}

$txn = mysqli_real_escape_string($conn, $_GET['txn']);

// Get all orders for this transaction
$sql = "SELECT * FROM tbl_order WHERE transaction_number='$txn'";
$res = mysqli_query($conn, $sql);
$orders = [];
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $orders[] = $row;
    }
} else {
    die("No orders found for this transaction.");
}

$total_amount = array_sum(array_column($orders, 'total'));
$customer_name = $orders[0]['customer_name'];
$table_number = $orders[0]['table_number'];
$order_date   = $orders[0]['order_date'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt - <?php echo $txn; ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt { width: 300px; margin: auto; }
        .receipt h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 4px; border-bottom: 1px solid #ddd; }
        .total { font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <h2>Restaurant Receipt</h2>
        <p><strong>Transaction:</strong> <?php echo $txn; ?><br>
        <strong>Customer:</strong> <?php echo htmlspecialchars($customer_name); ?><br>
        <strong>Table:</strong> <?php echo htmlspecialchars($table_number); ?><br>
        <strong>Date:</strong> <?php echo $order_date; ?></p>

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
        <p style="text-align:center;">Thank you for dining with us!</p>
    </div>
</body>
</html>

<?php include('partials/menu.php'); ?>
<?php include('partials/admin-check.php'); ?>



<div class="main-content">
    <div class="wrapper">
        <h1>Manage Orders Delivery</h1>

        <?php
        if(isset($_SESSION['update'])) {
            echo $_SESSION['update'];
            unset($_SESSION['update']);
        }

        if(isset($_SESSION['delete'])) {
            echo $_SESSION['delete'];
            unset($_SESSION['delete']);
        }
        ?>

        <?php
        // Fetch all takeout orders grouped by transaction
        $sql = "SELECT * FROM tbl_takeout ORDER BY order_date DESC";
        $res = mysqli_query($conn, $sql);
        $orders = [];
        if($res && mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){
                $orders[$row['transaction_number']][] = $row;
            }
        }
        ?>

        <?php if(!empty($orders)): ?>
            <div class="table-responsive">
            <table class="tbl-full">
                <tr>
                    <th>Transaction</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
                <?php foreach($orders as $txn => $items):
                    $total_amount = array_sum(array_column($items,'total'));
                    $item_summaries = [];
                    foreach($items as $o){ $item_summaries[] = htmlspecialchars($o['food']).' x'.$o['qty']; }
                    $items_str = implode(', ', $item_summaries);
                    $order_date = $items[0]['order_date'];
                    $customer = htmlspecialchars($items[0]['customer_name']);
                    $contact = htmlspecialchars($items[0]['customer_contact']);
                    $address = htmlspecialchars($items[0]['customer_address']);
                    $statuses = array_unique(array_column($items,'status'));
                    $status = count($statuses) === 1 ? htmlspecialchars($statuses[0]) : 'Mixed';
                    $payment = htmlspecialchars($items[0]['payment_method']);
                ?>
                <tr>
                    <td data-label="Transaction"><?php echo $txn; ?></td>
                    <td data-label="Customer"><?php echo $customer; ?></td>
                    <td data-label="Contact"><?php echo $contact; ?></td>
                    <td data-label="Address"><?php echo $address; ?></td>
                    <td data-label="Items"><?php echo $items_str; ?></td>
                    <td data-label="Total">₱<?php echo number_format($total_amount,2); ?></td>
                    <td data-label="Order Date"><?php echo $order_date; ?></td>
                    <td data-label="Status"><?php echo $status; ?></td>
                    <td data-label="Payment"><?php echo $payment; ?></td>
                    <td data-label="Actions">
                        <a href="<?php echo SITEURL; ?>admin/print-receipt-takeout.php?txn=<?php echo urlencode($txn); ?>" class="action-btn btn-primary" target="_blank"><i class="fa-solid fa-print"></i> Print</a>
                        <a href="<?php echo SITEURL; ?>admin/delete-transaction-takeout.php?txn=<?php echo urlencode($txn); ?>" class="action-btn btn-delete" onclick="return confirm('Delete ALL orders under this transaction?');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            </div>
        <?php else: ?>
            <p class="error">Orders Not Available</p>
        <?php endif; ?>
    </div>
</div>

<!-- Table styles are handled in css/admin.css -->

<?php include('partials/footer.php'); ?>

<?php include('partials/menu.php'); ?>

<?php include('partials/admin-check.php'); ?>

<div class="main-content">
    <div class="wrapper">
        <h1>Manage Orders Take-outs</h1>

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
        // Fetch all orders grouped by transaction
        $sql = "SELECT * FROM tbl_order ORDER BY order_date DESC";
        $res = mysqli_query($conn, $sql);
        $orders = [];
        if($res && mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){
                $orders[$row['transaction_number']][] = $row;
            }
        }
        ?>

        <?php if(!empty($orders)): ?>
            <?php foreach($orders as $txn => $items): ?>
                <?php $total_amount = array_sum(array_column($items,'total')); ?>
                <button class="collapsible" onclick="toggleCollapse(this, 'content_<?php echo $txn; ?>')">
                    <span class="arrow">▶</span> 
                    <?php echo $txn; ?> - <?php echo htmlspecialchars($items[0]['customer_name']); ?> | 
                    Table <?php echo htmlspecialchars($items[0]['table_number']); ?> | 
                    ₱<?php echo number_format($total_amount,2); ?>
                </button>
                <div class="content" id="content_<?php echo $txn; ?>">
                    <table class="tbl-full">
                        <tr>
                            <th>S.N</th>
                            <th>Food</th>
                            <th>Price</th>
                            <th>Qty.</th>
                            <th>Total</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                        <?php $sn=1; foreach($items as $order): ?>
                        <tr>
                            <td><?php echo $sn++; ?>.</td>
                            <td><?php echo htmlspecialchars($order['food']); ?></td>
                            <td><?php echo number_format($order['price'],2); ?></td>
                            <td><?php echo $order['qty']; ?></td>
                            <td><?php echo number_format($order['total'],2); ?></td>
                            <td><?php echo $order['order_date']; ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                            <td>
                                <a href="<?php echo SITEURL; ?>admin/update-order.php?id=<?php echo $order['id']; ?>" class="btn-secondary">Update</a>
                                <a href="<?php echo SITEURL; ?>admin/delete-order.php?id=<?php echo $order['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                                <a href="<?php echo SITEURL; ?>admin/print-receipt-dinein.php?txn=<?php echo urlencode($txn); ?>" class="btn-primary" target="_blank">Print Receipt</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- Total row -->
                        <tr class="total-row">
                            <td colspan="4" style="text-align:right;">Total:</td>
                            <td>₱<?php echo number_format($total_amount,2); ?></td>
                            <td colspan="3"></td>
                            <td>
                                <a href="<?php echo SITEURL; ?>admin/delete-transaction.php?txn=<?php echo urlencode($txn); ?>" 
                                   class="btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete ALL orders under this transaction?');">
                                   Delete All
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="error">Orders Not Available</p>
        <?php endif; ?>
    </div>
</div>

<style>
    .collapsible {
        cursor: pointer;
        padding: 10px;
        width: 100%;
        text-align: left;
        background-color: #f1f1f1;
        border: none;
        outline: none;
        font-size: 16px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .arrow {
        transition: transform 0.2s ease;
        display: inline-block;
    }

    .arrow.open {
        transform: rotate(90deg); /* ▶ turns ▼ */
    }

    .content {
        display: none;
        padding: 10px;
        background-color: #fafafa;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        overflow-x: auto;
    }

    .tbl-full th, .tbl-full td {
        padding: 8px 10px;
        text-align: left;
    }

    .total-row {
        font-weight: bold;
        background: #e9f5e9; /* light green highlight */
        border-top: 2px solid #4CAF50;
    }
</style>

<script>
    function toggleCollapse(button, id){
        var content = document.getElementById(id);
        var arrow = button.querySelector('.arrow');

        if(content.style.display === "block"){
            content.style.display = "none";
            arrow.classList.remove("open");
        } else {
            content.style.display = "block";
            arrow.classList.add("open");
        }
    }
</script>

<?php include('partials/footer.php'); ?>

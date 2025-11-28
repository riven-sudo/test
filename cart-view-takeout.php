<?php
include('config/constants.php');

// ✅ Allow guest checkout
$customer = null;
if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $sql_customer = "SELECT * FROM tbl_customer WHERE id = $customer_id";
    $res_customer = mysqli_query($conn, $sql_customer);
    $customer = mysqli_fetch_assoc($res_customer);
}

// ✅ Ensure cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<link rel="stylesheet" href="css/cart.css">

<?php if (!empty($_SESSION['cart'])): ?>
<form method="post" action="multiple-orders-takeout.php" enctype="multipart/form-data" id="orderForm" class="cart-container">

    <h3 class="cart-title">Delivery Summary</h3>

    <?php $grand_total = 0; ?>
    <?php foreach ($_SESSION['cart'] as $i => $item): ?>
        <?php 
            $line_total = $item['price'] * $item['qty']; 
            $grand_total += $line_total; 
        ?>
        <div class="cart-item" id="item-<?php echo $i; ?>">

            <div class="item-image">
                <?php if (!empty($item['image'])): ?>
                    <img src="<?php echo SITEURL; ?>images/food/<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['title']); ?>" 
                         width="80" height="80" class="img-curve">
                <?php else: ?>
                    <span class="no-image">No Image</span>
                <?php endif; ?>
            </div>

            <div class="item-info">
                <h4 class="item-name"><?php echo htmlspecialchars($item['title']); ?></h4>
                <p class="item-price">₱<?php echo number_format($item['price'], 2); ?> each</p>

                <div class="item-qty">
                    <button type="button" class="qty-btn" data-action="decrease" data-index="<?php echo $i; ?>">-</button>
                    <span id="qty-<?php echo $i; ?>"><?php echo $item['qty']; ?></span>
                    <button type="button" class="qty-btn" data-action="increase" data-index="<?php echo $i; ?>">+</button>
                </div>
            </div>

            <div class="item-actions">
                <p class="line-total">₱<?php echo number_format($line_total, 2); ?></p>
                <button type="button" class="btn-remove" data-index="<?php echo $i; ?>">🗑</button>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Customer Info -->
    <div class="customer-info">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($customer['username'] ?? 'Guest'); ?>" required>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>" required>

        <label>Contact:</label><br>
        <input type="text" name="contact" value="<?php echo htmlspecialchars($customer['contact'] ?? ''); ?>" required>

        <label>Address:</label><br>
        <input type="text" name="address" value="<?php echo htmlspecialchars($customer['address'] ?? ''); ?>" required>
    </div>

    <!-- Payment Method -->
    <div class="payment-method">
        <label>Payment Method:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="Cash">Cash</option>
            <option value="GCash">GCash</option>
        </select>
    </div>

    <!-- GCash Section -->
    <div id="gcash-section" class="gcash-card">
        <h4>📲 Pay with GCash</h4>
        <p><strong>GCash Number:</strong> 09362220898</p>
        <img src="images/qr-code.jpg" alt="GCash QR Code" width="180"><br>
        <small>Please upload your payment screenshot and provide your Reference Number.</small>

     

        <div class="gcash-upload">
            <label>Upload GCash Proof:</label><br>
            <input type="file" name="gcash_proof" accept="image/*">
        </div>
    </div>

    <!-- Order Summary -->
    <div class="cart-summary">
        <p>Subtotal: <span id="subtotal">₱<?php echo number_format($grand_total, 2); ?></span></p>
        <h3>Total: <span id="grand-total">₱<?php echo number_format($grand_total, 2); ?></span></h3>
    </div>

    <button type="submit" name="submit_order" class="submit-btn">✅ Submit Delivery Order</button>

</form>

<!-- ✅ JS for GCash toggle + remove item -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ DOM loaded — initializing GCash toggle & remove buttons.");

    const paymentSelect = document.getElementById('payment_method');
    const gcashSection = document.getElementById('gcash-section');

    if (paymentSelect && gcashSection) {
        const savedMethod = localStorage.getItem('selectedPaymentMethod');
        if (savedMethod) paymentSelect.value = savedMethod;

        function toggleGCash() {
            const selected = paymentSelect.value.trim().toLowerCase();
            console.log("💳 Payment selected:", selected);
            if (selected === 'gcash') {
                gcashSection.style.display = 'block';
                gcashSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                gcashSection.style.display = 'none';
            }
            localStorage.setItem('selectedPaymentMethod', paymentSelect.value);
        }

        paymentSelect.addEventListener('change', toggleGCash);
        toggleGCash(); // initialize
    }

    // ✅ Remove item logic
    document.querySelectorAll(".btn-remove").forEach(function (button) {
        button.addEventListener("click", function () {
            const index = this.dataset.index;
            if (!confirm("Remove this item?")) return;

            fetch("<?php echo SITEURL; ?>remove_from_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "remove=" + encodeURIComponent(index)
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    location.reload();
                } else {
                    alert("❌ Failed to remove item. Response: " + data);
                }
            })
            .catch(err => alert("Error: " + err));
        });
    });
});
</script>

<!-- ✅ CSS fix for GCash visibility -->
<style>
.gcash-card {
  display: none;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  padding: 15px;
  margin-top: 15px;
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<?php else: ?>
    <p>No items in cart. <a href="index.php">Go back to menu</a></p>
<?php endif; ?>

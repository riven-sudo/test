<?php
include_once(__DIR__ . '/config/constants.php');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<link rel="stylesheet" href="css/cart.css">

<?php if (!empty($_SESSION['cart'])): ?>
<form method="post" action="multiple-orders.php" id="orderForm" class="cart-container" enctype="multipart/form-data">

    <h3 class="cart-title">Takeout Summary</h3>

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
                    <button type="button" class="qty-btn" data-index="<?php echo $i; ?>" data-action="increase">+</button>
                </div>
            </div>

            <div class="item-actions">
                <p class="line-total" id="line-total-<?php echo $i; ?>">₱<?php echo number_format($line_total, 2); ?></p>
                <button type="button" class="btn-remove" data-index="<?php echo $i; ?>">🗑</button>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Customer Info -->
    <div class="customer-info">
        <label>Name:</label><br>
        <?php if (isset($_SESSION['customer'])): ?>
            <input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['customer']); ?>" readonly>
        <?php else: ?>
            <input type="text" name="name" required>
        <?php endif; ?>
    </div>

    <!-- Payment -->
    <div class="payment-method">
        <label>Payment Method:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="Cash">Cash</option>
            <option value="GCash">GCash</option>
        </select>
    </div>

    <!-- GCash QR and Upload -->
    <div id="gcash-section" class="gcash-card">
        <h4>📲 Scan to Pay with GCash</h4>
        <p><strong>GCash Number:</strong> 09362220898</p>
        <img src="<?php echo SITEURL; ?>images/qr-code.jpg" alt="GCash QR Code" width="180">
        <small>Please upload a screenshot of your payment below.</small>

        <div class="upload-proof" style="margin-top:10px;">
            <label>Upload GCash Screenshot:</label><br>
            <input type="file" name="gcash_proof" id="gcash_proof" accept="image/*">
            <div class="preview-container" style="margin-top:10px;">
                <img id="proof-preview" src="" alt="Preview" 
                     style="display:none; max-width:200px; border:1px solid #ccc; border-radius:8px;">
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="cart-summary">
        <p>Subtotal: <span id="subtotal">₱<?php echo number_format($grand_total, 2); ?></span></p>
        <h3>Total: <span id="grand-total">₱<?php echo number_format($grand_total, 2); ?></span></h3>
    </div>

    <button type="submit" name="submit_order" class="submit-btn">✅ Submit Take-out Orders</button>

</form>

<!-- ✅ JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentSelect = document.getElementById('payment_method');
    const gcashSection = document.getElementById('gcash-section');
    const proofInput = document.getElementById('gcash_proof');
    const proofPreview = document.getElementById('proof-preview');

    // Show/hide GCash section
    function toggleGCash() {
        const selected = paymentSelect.value.toLowerCase();
        if (selected === 'gcash') {
            gcashSection.style.display = 'block';
        } else {
            gcashSection.style.display = 'none';
            proofInput.value = '';
            proofPreview.src = '';
            proofPreview.style.display = 'none';
        }
    }

    // File preview
    proofInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                proofPreview.src = e.target.result;
                proofPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            proofPreview.src = '';
            proofPreview.style.display = 'none';
        }
    });

    paymentSelect.addEventListener('change', toggleGCash);
    toggleGCash(); // run on load
});
</script>

<!-- ✅ CSS -->
<style>
.gcash-card {
  display: none;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  padding: 15px;
  margin-top: 15px;
}
</style>

<?php else: ?>
    <p>No items in cart. <a href="index.php">Go back to menu</a></p>
<?php endif; ?>

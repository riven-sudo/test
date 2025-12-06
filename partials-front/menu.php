<?php 
include('config/constants.php'); 
$loggedInUser = $_SESSION['customer'] ?? null;

$total_qty = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_qty += $item['qty'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blackstar</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

   <section class="navbar">
      <div class="container">
        
        <!-- Left -->
        <div class="navbar-left">
          <a href="<?php echo SITEURL; ?>">
            <img src="images/background.jpg" alt="Blackstar Logo">
          </a>
        </div>

        <!-- Desktop Menu -->
        <div class="menu desktop-menu">
          <ul>
            <li><a href="<?php echo SITEURL; ?>">Home</a></li>
            <li><a href="<?php echo SITEURL; ?>foods.php">Foods</a></li>
            <li><a href="<?php echo SITEURL; ?>profile.php">Profile</a></li>
             
          </ul>
        </div>

        <!-- Mobile Hamburger -->
        <div class="hamburger mobile-only" onclick="toggleMenu()">&#9776;</div>

        <!-- Side navigation (mobile) -->
        <div id="sideMenu" class="side-menu">
          <a href="javascript:void(0)" class="closebtn" onclick="toggleMenu()">&times;</a>
          <a href="<?php echo SITEURL; ?>">Home</a>
          
        
          <a href="<?php echo SITEURL; ?>foods.php">Foods</a>
          <a href="<?php echo SITEURL; ?>profile.php">Profile</a>
        </div>

        <!-- Right -->
        <div class="navbar-right">
            <?php if ($loggedInUser): ?>
                <span style="margin-right: 10px; color: orange;">
                    <?php echo htmlspecialchars($loggedInUser); ?>
                </span>
                <button class="glow-btn" onclick="window.location.href='logout-customer.php';">
                    Logout
                </button>
            <?php else: ?>
                <button class="glow-btn" onclick="window.location.href='customer-login.php';">
                    Login
                </button>
            <?php endif; ?>
            
            <!-- 🛒 Now inside navbar -->
          <button id="viewOrderBtn" onclick="openCart('dinein')" class="glow-btn" style="position: relative;">
  🛒<span>View Orders</span>
  <?php if ($total_qty > 0): ?>
    <span class="cart-notification"><?php echo $total_qty; ?></span>
  <?php endif; ?>
</button>


            
            <button id="darkModeToggle" class="darkmode-btn">🌙</button>
        </div>

      </div>
   </section>

  <!-- Shared Cart Panel -->
<div id="cartPanel" style="
      position:fixed;
      top:0;
      right:-100%;
      width:400px;
      max-width:100%;
      height:100%;
      background:#fff;
      box-shadow:-2px 0 10px rgba(0,0,0,0.3);
      z-index:3000; /* 🔥 higher than navbar */
      transition:right 0.4s ease;
      overflow:auto;
   ">
      <div style="padding:15px; border-bottom:1px solid #ccc; display:flex; justify-content:space-between; align-items:center;">
        <h2 id="cartTitle" style="margin:0;">Your Cart</h2>
        <button onclick="closeCart()">❌</button>
      </div>
      <div id="cartContent" style="padding:15px;">
        Loading...
      </div>
      <div style="padding:15px; border-top:1px solid #ccc; text-align:center;">
        <button id="switchCartBtn" onclick="switchCart()">📦 Switch to Delivery</button>
      </div>
</div>

<div id="cartOverlay" style="
      display:none;
      position:fixed;
      top:0;
      left:0;
      width:100%;
      height:100%;
      background:rgba(0,0,0,0.5);
      z-index:2500; /* 🔥 higher than page but below cart */
   " onclick="closeCart()"></div>


<script>
let currentCart = "dinein"; // default cart type

function openCart(type = "dinein") {
  currentCart = type;
  document.getElementById("cartOverlay").style.display = "block";
  document.getElementById("cartPanel").style.right = "0";
  loadCart();
}

function closeCart() {
  document.getElementById("cartOverlay").style.display = "none";
  document.getElementById("cartPanel").style.right = "-100%";
}

function switchCart() {
  currentCart = (currentCart === "dinein") ? "takeout" : "dinein";
  loadCart();
}

function loadCart() {
  const url = (currentCart === "takeout") ? "cart-view-takeout.php" : "cart-view.php";
  const title = "🛒Your Cart";
  const btnText = (currentCart === "takeout") ? "🍽 Switch to Take-out" : "📦 Switch to Delivery";

  document.getElementById("cartTitle").textContent = title;
  document.getElementById("switchCartBtn").textContent = btnText;

  fetch(url)
    .then(res => res.text())
    .then(data => {
      document.getElementById("cartContent").innerHTML = data;

      // ✅ Initialize GCash toggle after new cart loads
      initializeGCashToggle();
    });
}

/* ---------------------------
   GCash toggle initializer
----------------------------*/
function initializeGCashToggle() {
  const paymentSelect = document.querySelector('#payment_method');
  const gcashSection = document.querySelector('#gcash-section');

  if (!paymentSelect || !gcashSection) {
    console.log("⚠️ No GCash elements found (please select an order).");
    return;
  }

  console.log("✅ GCash toggle initialized.");

  // Restore saved method if exists
  const savedMethod = localStorage.getItem('selectedPaymentMethod');
  if (savedMethod) {
    paymentSelect.value = savedMethod;
  }

  function toggleGCash() {
    const selected = paymentSelect.value.trim().toLowerCase();
    console.log("💳 Payment method selected:", selected);

    if (selected === 'gcash') {
      gcashSection.style.display = 'block';
      gcashSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
      gcashSection.style.display = 'none';
    }

    localStorage.setItem('selectedPaymentMethod', paymentSelect.value);
  }

  // Initialize + Listen for changes
  toggleGCash();
  paymentSelect.addEventListener('change', toggleGCash);
}

/* ---------------------------
   Remove item listener
----------------------------*/
document.addEventListener("click", function(e) {
  if (e.target.classList.contains("btn-remove")) {
    const index = e.target.getAttribute("data-index");
    if (confirm("Remove this item?")) {
      fetch("<?php echo SITEURL; ?>remove_from_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "remove=" + encodeURIComponent(index)
      })
      .then(res => res.text())
      .then(data => {
        if (data.trim() === "success") {
          loadCart(); // reload updated cart
        } else {
          alert("Failed to remove item. Server said: " + data);
        }
      });
    }
  }
});

/* ---------------------------
   Menu toggle for mobile
----------------------------*/
function toggleMenu() {
  const menu = document.getElementById("sideMenu");
  menu.style.width = (menu.style.width === "250px") ? "0" : "250px";
}
</script>

<style>
/* ✅ Ensure GCash section visibility works */
.gcash-card {
  display: none;
}
.gcash-card.show,
.gcash-card[style*="block"] {
  display: block !important;
}



<style>
@media screen and (max-width: 768px) {
  #cartPanel {
    width: 100%;
  }
}
</style>

<script>
  const toggleBtn = document.getElementById("darkModeToggle");
  
  if (localStorage.getItem("dark-mode") === "enabled") {
    document.body.classList.add("dark-mode");
  }

  toggleBtn.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
      localStorage.setItem("dark-mode", "enabled");
    } else {
      localStorage.setItem("dark-mode", "disabled");
    }
  });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ cart-view.js loaded");

    // update qty
    document.querySelectorAll(".qty-btn").forEach(function (button) {
        button.addEventListener("click", function () {
            let index = this.dataset.index;
            let action = this.dataset.action;

            console.log("🔘 Button clicked:", { index, action }); // debug click

            fetch("update_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "index=" + index + "&action=" + action
            })
            .then(res => {
                console.log("📩 Raw response:", res); // debug response
                return res.json();
            })
            .then(data => {
                console.log("📊 Parsed JSON:", data); // debug parsed JSON
                if (data.status === "success") {
                    document.querySelector("#qty-" + index).innerText = data.qty;
                    document.querySelector("#line-total-" + index).innerText = "₱" + data.line_total;
                    document.querySelector("#subtotal").innerText = "₱" + data.grand_total;
                    document.querySelector("#grand-total").innerText = "₱" + data.grand_total;
                } else {
                    console.warn("❌ Error status from server:", data.message);
                    alert("❌ " + data.message);
                }
            })
            .catch(err => {
                console.error("⚠️ Fetch error:", err);
                alert("Error: " + err);
            });
        });
    });

    // remove item
    document.querySelectorAll(".btn-remove").forEach(function (button) {
        button.addEventListener("click", function () {
            let index = this.dataset.index;
            console.log("🗑 Remove button clicked:", index); // debug

            fetch("remove_from_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "remove=" + index
            })
            .then(res => res.text())
            .then(data => {
                console.log("🗑 Remove response:", data); // debug
                if (data.trim() === "success") {
                    document.querySelector("#item-" + index).remove();

                    // recalc grand total (optional: reload if easier)
                    location.reload();
                } else {
                    console.warn("❌ Failed to remove item:", data);
                    alert("❌ Failed to remove item: " + data);
                }
            })
            .catch(err => {
                console.error("⚠️ Fetch error:", err);
                alert("Error: " + err);
            });
        });
    });
});
</script>

<script>
/* put this in menu.php near the end (before </body>) */
(function(){
  const updateUrl = "<?php echo rtrim(SITEURL, '/'); ?>/update_cart.php";
  const removeUrl = "<?php echo rtrim(SITEURL, '/'); ?>/remove_from_cart.php";

  document.addEventListener('click', async function(e){
    // ---------- qty buttons (works with dynamic content) ----------
    const qtyBtn = e.target.closest('.qty-btn');
    if (qtyBtn) {
      e.preventDefault();
      const index = qtyBtn.dataset.index;
      const action = qtyBtn.dataset.action;
      console.log('🔘 qty click', { index, action });

      try {
        const r = await fetch(updateUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'index=' + encodeURIComponent(index) + '&action=' + encodeURIComponent(action)
        });

        console.log('📩 raw response', r, 'status', r.status);
        const ctype = r.headers.get('content-type') || '';
        if (!ctype.includes('application/json')) {
          const txt = await r.text();
          console.error('⚠️ update_cart did not return JSON:', txt);
          alert('Server error (see console).');
          return;
        }

        const data = await r.json();
        console.log('📊 update_cart JSON:', data);

        if (data.status === 'success') {
  // update qty + totals
  const qtyEl = document.querySelector('#qty-' + index);
  const lineEl = document.querySelector('#line-total-' + index);
  if (qtyEl) qtyEl.textContent = data.qty;
  if (lineEl) lineEl.textContent = '₱' + parseFloat(data.line_total).toFixed(2);
  const sub = document.querySelector('#subtotal');
  const grand = document.querySelector('#grand-total');
  if (sub) sub.textContent = '₱' + parseFloat(data.grand_total).toFixed(2);
  if (grand) grand.textContent = '₱' + parseFloat(data.grand_total).toFixed(2);

} else if (data.status === 'removed') {
  // 🔥 If item removed, reload cart content
  if (typeof loadCart === 'function') loadCart();
  else location.reload();
} else {
  alert('Update failed: ' + (data.message || 'unknown'));
}

      } catch (err) {
        console.error('⚠️ fetch error (update_cart):', err);
        alert('Network error. Check console.');
      }

      return; // done
    }

    // ---------- remove buttons (works with dynamic content) ----------
    const remBtn = e.target.closest('.btn-remove');
    if (remBtn) {
      e.preventDefault();
      const index = remBtn.dataset.index;
      if (!confirm('Remove this item?')) return;

      console.log('🗑 remove click', index);
      try {
        const r = await fetch(removeUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'remove=' + encodeURIComponent(index)
        });

        const txt = await r.text();
        console.log('🗑 remove response (raw):', txt);
        if (txt.trim() === 'success') {
          // reload overlay content after successful removal
          if (typeof loadCart === 'function') loadCart();
          else location.reload();
        } else {
          alert('Remove failed: ' + txt);
        }
      } catch (err) {
        console.error('⚠️ fetch error (remove):', err);
        alert('Network error. Check console.');
      }
      return;
    }
  });
})();


</script>
<style>
  /* --- Compact Sticky Navbar --- */
.navbar {
  position: sticky;
  top: 0;
  background: #fff;               /* keep it visible when scrolling */
  padding: 5px 20px;              /* reduce padding */
  height: 60px;                   /* smaller height */
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  z-index: 2000;
  display: flex;
  align-items: center;
  
}

/* Keep items aligned horizontally */
.navbar .container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Shrink the logo */
.navbar-left img {
  height: 45px;                   /* smaller logo */
  width: auto;
  border-radius: 50%;
}

/* Adjust menu links */
.menu ul {
  display: flex;
  gap: 10px;
}

.menu ul li a {
  font-size: 0.95rem;
  padding: 6px 10px;              /* smaller click area */
  line-height: 1;
}

/* Make buttons match the smaller size */
.glow-btn {
  padding: 6px 12px;
  font-size: 0.85rem;
}

/* Optional: cart notification circle smaller */
.cart-notification {
  top: -8px;
  right: -8px;
  font-size: 11px;
  padding: 2px 6px;
}



/* Responsive tweak */
@media (max-width: 768px) {
  .navbar {
    height: auto;
    padding: 8px 12px;
  }
  .navbar-left img {
    height: 40px;
  }
}

</style>
</body>
</html>

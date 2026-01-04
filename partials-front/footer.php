<!-- Footer Section Starts -->
<footer class="footer">
    <div class="footer-container">
        <!-- About -->
        <div class="footer-about">
            <h3>🍴 Blackstar Snackbar</h3>
            <p>Serving delicious meals since 2011. Fast, fresh, and made with love.</p>
        </div>

        <!-- Links -->
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo SITEURL; ?>">Home</a></li>
                <li><a href="<?php echo SITEURL; ?>categories.php">Categories</a></li>
                <li><a href="<?php echo SITEURL; ?>foods.php">Menu</a></li>
                <li><a href="<?php echo SITEURL; ?>contact.php">Contact</a></li>
            </ul>
        </div>

        <!-- Contact -->
        <div class="footer-contact">
            <h4>Contact Us</h4>
            <p>📍 Calaocan, Bambang, Nueva Vizcaya</p>
            <p>📞 +63 912 345 6789</p>
            <div class="footer-socials">
                <a href="https://www.facebook.com/blackstar.snackbar/" target="_blank">Facebook</a>
                <a href="#">Instagram</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© <?php echo date("Y"); ?> Blackstar Snackbar | Since 2011</p>
    </div>
</footer>

<script>
function chooseOrder() {
  let dineIn = confirm("Click OK for Dine-In, Cancel for Take-Out");
  if (dineIn) {
    window.location.href = "manage-order-dinein.php";
  } else {
    window.location.href = "manage-order-takeout.php"; 
  }
  return false;
}
</script>

</body>
</html>

<style>
/* Footer Styling */
.footer {
    background: #222;
    color: #ddd;
    padding: 40px 20px 10px;
    font-family: Arial, sans-serif;
}

.footer-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: auto;
}

.footer h3, 
.footer h4 {
    color: #fff;
    margin-bottom: 10px;
}

.footer p {
    font-size: 14px;
    line-height: 1.6;
}

.footer-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links ul li {
    margin: 6px 0;
}

.footer-links ul li a {
    color: #ddd;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-links ul li a:hover {
    color: #f39c12;
}

.footer-contact p {
    margin: 5px 0;
    font-size: 14px;
}

.footer-socials a {
    margin-right: 10px;
    color: #ddd;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-socials a:hover {
    color: #f39c12;
}

.footer-bottom {
    text-align: center;
    margin-top: 30px;
    padding-top: 15px;
    border-top: 1px solid #444;
    font-size: 14px;
    color: #bbb;
}
</style>

<!--Footer Section Starts-->
<div class="footer">
            <div class="wrapper">
                <p class="text-center">Since 2011. <a href="https://www.facebook.com/blackstar.snackbar/">BLACKSTAR</a></p>
            </div>
        </div>
        <script>
function chooseOrder() {
  let dineIn = confirm("Click OK for Dine-In, Cancel for Take-Out");

  if (dineIn) {
    // Go to dine-in orders
    window.location.href = "manage-order-dinein.php";
  } else {
    // Go to take-out orders
    window.location.href = "manage-order-takeout.php"; 
  }

  return false; // stop the default link
}
</script>


        <!--Footer Section Ends-->
    </body>
</html>

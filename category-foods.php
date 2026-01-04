<?php  
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

include('config/constants.php');

// Get category_id
$category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;

// --- Handle Add to Cart ---
if (isset($_POST['add_to_cart'])) {
    $food_id = (int) $_POST['food_id'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_food WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $food_id);
    mysqli_stmt_execute($stmt);
    $res_food = mysqli_stmt_get_result($stmt);

    if ($res_food && mysqli_num_rows($res_food) > 0) {
        $food = mysqli_fetch_assoc($res_food);
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $food_id) {
                $item['qty'] += 1;
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $food['id'],
                'title' => $food['title'],
                'price' => $food['price'],
                'qty' => 1,
                'image' => $food['image_name']
            ];
        }
        $_SESSION['cart_message'] = "✅ '{$food['title']}' added to cart!";
    }
    mysqli_stmt_close($stmt);
    header("Location: category-foods.php?category_id=" . $category_id);
    exit();
}

// --- Handle Rating Submission ---
if (isset($_POST['submit_rating'])) {
    $food_id = (int) $_POST['food_id'];
    $rating = (int) $_POST['submit_rating'];
    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_ratings (food_id, rating, created_at) VALUES (?, ?, NOW())");
    mysqli_stmt_bind_param($stmt, "ii", $food_id, $rating);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['rating_message'] = "⭐ Thanks for rating!";
    header("Location: category-foods.php?category_id=" . $category_id);
    exit();
}

// --- Validate Category ---
if ($category_id <= 0) {
    header('location:' . SITEURL);
    exit();
}
$stmt = mysqli_prepare($conn, "SELECT title FROM tbl_category WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $category_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    $category_title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
} else {
    mysqli_stmt_close($stmt);
    header('location:' . SITEURL);
    exit();
}
mysqli_stmt_close($stmt);

include('partials-front/menu.php'); 
?>
<link rel="stylesheet" href="css/category-foods.css">

<!-- Category Header -->
<section class="food-search text-center">
    <div class="container">
        <h2>Foods on <span class="category-highlight">"<?php echo $category_title; ?>"</span></h2>
    </div>
</section>

<!-- Messages -->
<?php
if (isset($_SESSION['cart_message'])) { echo "<p class='alert success'>".$_SESSION['cart_message']."</p>"; unset($_SESSION['cart_message']); }
if (isset($_SESSION['rating_message'])) { echo "<p class='alert info'>".$_SESSION['rating_message']."</p>"; unset($_SESSION['rating_message']); }
?>

<!-- Food Menu -->
<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Food Menu</h2>
        <div class="food-grid">
        <?php
        $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_food WHERE category_id = ? AND active = 'Yes'");
        mysqli_stmt_bind_param($stmt, "i", $category_id);
        mysqli_stmt_execute($stmt);
        $res2 = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($res2) > 0) {
            while ($row2 = mysqli_fetch_assoc($res2)) {
                $id = $row2['id'];
                $title = htmlspecialchars($row2['title']);
                $price = number_format($row2['price'], 2);
                $desc = htmlspecialchars($row2['description']);
                $img = $row2['image_name'];

                // Get average rating
                $res_avg = mysqli_query($conn, "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM tbl_ratings WHERE food_id = $id");
                $row_avg = mysqli_fetch_assoc($res_avg);
                $avg_rating = $row_avg['total_ratings'] > 0 ? round($row_avg['avg_rating'], 1) : 0;
                $total_ratings = $row_avg['total_ratings'];
        ?>
            <div class="food-card">
                <div class="food-img">
                    <?php if ($img != "" && file_exists("images/food/" . $img)) { ?>
                        <img src="<?php echo SITEURL; ?>images/food/<?php echo $img; ?>" alt="<?php echo $title; ?>">
                    <?php } else { echo "<div class='no-image'>No Image</div>"; } ?>
                </div>

                <div class="food-info">
                    <small class="rating-left">
   <?php 
if ($total_ratings > 0) {
    $fullStars = floor($avg_rating);
    $halfStar  = ($avg_rating - $fullStars >= 0.5) ? 1 : 0; 
    $emptyStars = 5 - $fullStars - $halfStar;

    // full stars
    for ($i=0; $i < $fullStars; $i++) { echo "<span class='star full'>★</span>"; }
    // half star (optional)
    if ($halfStar) { echo "<span class='star half'>★</span>"; }
    // empty stars
    for ($i=0; $i < $emptyStars; $i++) { echo "<span class='star empty'>☆</span>"; }

    echo " <span class='rating-text'>($avg_rating / 5 from $total_ratings ratings)</span>";
} else {
    // show 5 empty stars para uniform ang design
    for ($i=0; $i < 5; $i++) { echo "<span class='star empty'>☆</span>"; }
    echo " <span class='no-rating'>(No ratings yet)</span>";
}
?>

</small>

                    <h4><?php echo $title; ?></h4>
                    <p class="food-price">₱<?php echo $price; ?></p>
                    <p class="food-detail"><?php echo $desc; ?></p>
                </div>

                <div class="food-actions">
                    <!-- Add to Cart -->
                    <form method="POST">
                        <input type="hidden" name="food_id" value="<?php echo $id; ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">🛒 Add to Cart</button>
                    </form>

                    <!-- Rating -->
                    <form method="POST" class="stars" title="Rate this food">
                        <input type="hidden" name="food_id" value="<?php echo $id; ?>">
                        <button type="submit" name="submit_rating" value="1" data-tooltip="Poor">⭐</button>
                        <button type="submit" name="submit_rating" value="2" data-tooltip="Fair">⭐</button>
                        <button type="submit" name="submit_rating" value="3" data-tooltip="Good">⭐</button>
                        <button type="submit" name="submit_rating" value="4" data-tooltip="Very Good">⭐</button>
                        <button type="submit" name="submit_rating" value="5" data-tooltip="Excellent">⭐</button>
                    </form>
                </div>
            </div>
        <?php
            }
        } else {
            echo "<p class='error'>No food items available in this category.</p>";
        }
        mysqli_stmt_close($stmt);
        ?>
        </div>
    </div>
</section>

<style>
/* Alerts */
.alert { text-align:center; padding:10px; margin:10px auto; width:80%; border-radius:5px; }
.alert.success { background:#d4edda; color:#155724; }
.alert.info { background:#cce5ff; color:#004085; }

/* Food Grid */
.food-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
.food-card {
  background:#fff;
  border-radius:10px;
  box-shadow:0 4px 10px rgba(0,0,0,0.1);
  padding:15px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
}
.food-img img { width:100%; border-radius:10px; }
.food-info { margin:10px 0; flex-grow: 1; }
.food-price { font-weight:bold; color:#e74c3c; }

/* Ratings */
.rating-left { font-size:14px; display:block; margin-bottom:5px; }
.star.full { color:#f1c40f; }
.star.half { color:#f1c40f; opacity:0.6; }
.star.empty { color:#ccc; }
.no-rating { color:#888; font-style:italic; }
.rating-text { font-size:12px; color:#555; }

/* Actions */
.food-actions {
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 5px;
}

/* Stars rating (input form) */
.stars { display:flex; gap:2px; margin-top:8px; }
.stars button {
  background:none;
  border:none;
  font-size:18px;
  cursor:pointer;
  color:#ccc;
  padding:0 2px;
  transition: transform 0.2s, color 0.2s;
  position: relative;
}
.stars button:hover { color:#f1c40f; transform: scale(1.2); }
.stars button:hover::after {
  content: attr(data-tooltip);
  position:absolute;
  bottom:125%;
  left:50%;
  transform:translateX(-50%);
  background:#333;
  color:#fff;
  padding:2px 6px;
  border-radius:4px;
  font-size:12px;
  white-space:nowrap;
  z-index:10;
}
.stars button:hover::before {
  content:'';
  position:absolute;
  bottom:115%;
  left:50%;
  transform:translateX(-50%);
  border-width:5px;
  border-style:solid;
  border-color:#333 transparent transparent transparent;
}
</style>

<?php include('partials-front/footer.php'); ?>

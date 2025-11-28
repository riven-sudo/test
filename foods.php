<?php include('partials-front/menu.php'); ?>

<?php
// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $food_id = (int) $_POST['food_id'];
    $sql_food = "SELECT * FROM tbl_food WHERE id = $food_id LIMIT 1";
    $res_food = mysqli_query($conn, $sql_food);
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
}
?>

<!-- Food Search -->
<section class="food-search text-center">
    <div class="container">
        <form action="<?php echo SITEURL; ?>food-search.php" method="POST">
            <input type="search" name="search" placeholder="Search for Food.." required>
            <input type="submit" name="submit" value="Search" class="btn btn-primary">
        </form>
    </div>
</section>

<?php
if (isset($_SESSION['cart_message'])) {
    echo "<p class='alert success'>" . $_SESSION['cart_message'] . "</p>";
    unset($_SESSION['cart_message']);
}
?>

<!-- Food Menu -->
<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Food Menu</h2>

        <!-- 🔹 Filter Buttons -->
        <div class="filter-buttons">
            <button class="filter-btn active" data-category="all">All</button>
            <button class="filter-btn" data-category="burger">Burger</button>
            <button class="filter-btn" data-category="drinks">Drinks</button>
            <button class="filter-btn" data-category="Rices">Rice</button>
            <button class="filter-btn" data-category="dumpling">dumplings</button>
        </div>

        <div class="food-grid">
            <?php
            $sql = "SELECT f.*, c.title AS category_title 
                    FROM tbl_food f 
                    LEFT JOIN tbl_category c ON f.category_id = c.id 
                    WHERE f.active='Yes'";
            $res = mysqli_query($conn, $sql);

            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $price = $row['price'];
                    $description = $row['description'];
                    $image_name = $row['image_name'];
                    $category_title = strtolower($row['category_title']);

                    // ✅ derive category safely from DB category name
                    $category = 'other';
                    if (strpos($category_title, 'burger') !== false) $category = 'burger';
                    elseif (strpos($category_title, 'drink') !== false || strpos($category_title, 'tea') !== false) $category = 'drinks';
                    elseif (strpos($category_title, 'rice') !== false) $category = 'Rices';
                    elseif (strpos($category_title, 'dumpling') !== false) $category = 'dumpling';

                    // Fetch average rating
                    $sql_avg = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings 
                                FROM tbl_ratings WHERE food_id = $id";
                    $res_avg = mysqli_query($conn, $sql_avg);
                    $row_avg = mysqli_fetch_assoc($res_avg);
                    $avg_rating = $row_avg['total_ratings'] > 0 ? round($row_avg['avg_rating'], 1) : 0;
                    $total_ratings = $row_avg['total_ratings'];
            ?>
                    <div class="food-card" data-category="<?php echo $category; ?>">
                        <div class="food-img">
                            <?php if ($image_name != "") { ?>
                                <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo $title; ?>">
                            <?php } else { echo "<div class='error'>Image not Available</div>"; } ?>
                        </div>

                        <div class="food-info">
                            <div class="rating-display">
                                <?php
                                if ($total_ratings > 0) {
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= round($avg_rating) ? "<span class='star filled'>★</span>" : "<span class='star'>★</span>";
                                    }
                                    echo "<small> ($avg_rating / 5 from $total_ratings ratings)</small>";
                                } else {
                                    for ($i = 1; $i <= 5; $i++) echo "<span class='star no-rating'>★</span>";
                                    echo " <small>(No ratings yet)</small>";
                                }
                                ?>
                            </div>

                            <h4><?php echo $title; ?></h4>
                            <p class="food-price">₱<?php echo $price; ?></p>
                            <p class="food-detail"><?php echo $description; ?></p>
                        </div>

                        <div class="food-actions">
                            <form method="POST" action="foods.php">
                                <input type="hidden" name="food_id" value="<?php echo $id; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary">🛒 Add to Cart</button>
                            </form>

                            <form method="POST" action="foods.php" class="stars-horizontal">
                                <input type="hidden" name="food_id" value="<?php echo $id; ?>">
                                <?php for ($i = 1; $i <= 5; $i++) { ?>
                                    <button type="submit" name="submit_rating" value="<?php echo $i; ?>">⭐</button>
                                <?php } ?>
                            </form>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='error'>Food not found</div>";
            }
            ?>
        </div>
    </div>
</section>

<!-- 🔹 Filter Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const filterButtons = document.querySelectorAll(".filter-btn");
  const foodCards = document.querySelectorAll(".food-card");

  filterButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      filterButtons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      const category = btn.dataset.category;

      foodCards.forEach(card => {
        if (category === "all" || card.dataset.category === category) {
          card.style.display = "flex";
          card.style.opacity = "1";
        } else {
          card.style.opacity = "0";
          setTimeout(() => (card.style.display = "none"), 200);
        }
      });
    });
  });
});
</script>

<style>
/* Alerts */
.alert { text-align:center; padding:10px; margin:10px auto; width:80%; border-radius:5px; }
.alert.success { background:#d4edda; color:#155724; }

/* Filter Buttons */
.filter-buttons {
  text-align: center;
  margin-bottom: 25px;
}
.filter-btn {
  background: #f4f4f4;
  border: none;
  padding: 8px 15px;
  margin: 5px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.95rem;
}
.filter-btn.active,
.filter-btn:hover {
  background: #f39c12;
  color: #fff;
}

/* Food Grid */
.food-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 18px;
  justify-content: center;
  align-items: stretch;
}

/* Food Card */
.food-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  text-align: center;
  padding: 12px;
  transition: all 0.2s ease;
  height: 400px;
  overflow: hidden;
}
.food-card:hover { transform: translateY(-4px); }

/* Image section */
.food-img {
  width: 100%;
  height: 160px;
  overflow: hidden;
  border-radius: 10px;
}
.food-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
}

/* Info Section */
.food-info { flex-grow: 1; display: flex; flex-direction: column; justify-content: flex-start; margin-top: 8px; width: 100%; }
.food-info h4 { font-size: 1rem; margin: 6px 0; line-height: 1.3; }
.food-price { font-weight: bold; color: #e74c3c; margin-bottom: 5px; }
.food-detail { font-size: 0.9rem; color: #555; min-height: 36px; max-height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }

/* Ratings */
.rating-display { margin-bottom: 5px; font-size: 13px; min-height: 18px; }
.star.filled { color: #f1c40f; }
.star { color: #ccc; }
.star.no-rating { color: rgba(0,0,0,0.1); }

/* Actions Section */
.food-actions { display: flex; flex-direction: column; align-items: center; gap: 6px; margin-top: auto; width: 100%; }
.food-actions .btn { font-size: 0.85rem; padding: 6px 10px; background: #f39c12; color: #fff; border: none; border-radius: 6px; cursor: pointer; transition: 0.2s; }
.food-actions .btn:hover { background: #e67e22; }

/* Clickable Stars */
.stars-horizontal { display: flex; gap: 3px; }
.stars-horizontal button { background: none; border: none; font-size: 16px; cursor: pointer; padding: 0; transition: transform 0.15s, color 0.15s; }
.stars-horizontal button:hover { transform: scale(1.2); color: #f1c40f; }

/* Responsive */
@media (max-width: 992px) {
  .food-card { height: auto; min-height: 370px; }
  .food-detail { max-height: none; -webkit-line-clamp: unset; }
}
@media (max-width: 768px) {
  .food-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }
  .food-card { height: auto; min-height: 340px; }
}
</style>

<?php include('partials-front/footer.php'); ?>

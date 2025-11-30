<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('config/constants.php'); // Make sure this has PDO connection
include('partials-front/menu.php');

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $food_id = (int)$_POST['food_id'];
    $stmt_food = $conn->prepare('SELECT * FROM tbl_food WHERE id = :id LIMIT 1');
    $stmt_food->execute(['id' => $food_id]);
    $food = $stmt_food->fetch(PDO::FETCH_ASSOC);

    if ($food) {
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
        $_SESSION['cart_message'] = "✅ '{$food['title']}' was added to your cart!";
    }
}

// Submit Rating
if (isset($_POST['submit_rating'])) {
    $food_id = (int)$_POST['food_id'];
    $rating = (int)$_POST['submit_rating'];

    $stmt_rate = $conn->prepare('INSERT INTO tbl_ratings (food_id, rating, created_at) VALUES (:food_id, :rating, NOW())');
    $stmt_rate->execute([
        'food_id' => $food_id,
        'rating' => $rating
    ]);

    $_SESSION['rating_message'] = "⭐ Thanks for rating this food!";
}

// ================= Dynamic Stats =================

// Happy Customers
$sql_customers = '
SELECT COUNT(*) AS total_customers FROM (
    SELECT DISTINCT customer_name FROM tbl_order WHERE customer_name IS NOT NULL AND customer_name != \'\'
    UNION
    SELECT DISTINCT customer_name FROM tbl_takeout WHERE customer_name IS NOT NULL AND customer_name != \'\'
) AS combined
';
$stmt_customers = $conn->query($sql_customers);
$row_customers = $stmt_customers->fetch(PDO::FETCH_ASSOC);
$happy_customers = $row_customers['total_customers'] ?? 0;

// Menu Items
$stmt_menu = $conn->query('SELECT COUNT(*) AS total_menu FROM tbl_food WHERE active=\'Yes\'');
$row_menu = $stmt_menu->fetch(PDO::FETCH_ASSOC);
$menu_items = $row_menu['total_menu'] ?? 0;

// Average Delivery
$stmt_delivery = $conn->query('
    SELECT AVG(EXTRACT(EPOCH FROM (NOW() - order_date))/60) AS avg_delivery
    FROM tbl_order
    WHERE status=\'Delivered\'
');
$row_delivery = $stmt_delivery->fetch(PDO::FETCH_ASSOC);
$avg_delivery = !empty($row_delivery['avg_delivery']) ? round($row_delivery['avg_delivery']) . ' min' : '15 min';

// Customer Rating
$stmt_rating = $conn->query('SELECT AVG(rating) AS avg_rating FROM tbl_ratings');
$row_rating = $stmt_rating->fetch(PDO::FETCH_ASSOC);
$rating = !empty($row_rating['avg_rating']) ? round($row_rating['avg_rating'], 1) . '⭐' : 'No Ratings';
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
if (isset($_SESSION['order'])) { echo $_SESSION['order']; unset($_SESSION['order']); }
if (isset($_SESSION['cart_message'])) { echo "<p class='alert success'>" . $_SESSION['cart_message'] . "</p>"; unset($_SESSION['cart_message']); }
if (isset($_SESSION['rating_message'])) { echo "<p class='alert info'>" . $_SESSION['rating_message'] . "</p>"; unset($_SESSION['rating_message']); }
?>

<!-- Explore Food Section -->
<section class="explore-food">
    <h2 class="carousel-title">Explore Food</h2>
    <div class="shuffle-container">
        <button class="carousel-btn left" onclick="slideLeft()">&#10094;</button>
        <div class="card-stack" id="cardStack">
            <!-- Cards loaded dynamically by JS -->
        </div>
        <button class="carousel-btn right" onclick="slideRight()">&#10095;</button>
    </div>
</section>

<!-- Food Menu -->
<section id="food-menu" class="food-menu">
    <video autoplay muted loop class="explore-bg-video">
        <source src="bg-video.mp4" type="video/mp4">
    </video>
    <div class="container">
        <h2 class="text-center">Food Menu</h2>
        <div class="food-grid">
        <?php
        $stmt_foods = $conn->query('SELECT * FROM tbl_food WHERE active=\'Yes\' AND featured=\'Yes\' LIMIT 4');
        $foods_featured = $stmt_foods->fetchAll(PDO::FETCH_ASSOC);

        if ($foods_featured) {
            foreach ($foods_featured as $row) {
                $id = $row['id'];
                $title = $row['title'];
                $price = $row['price'];
                $description = $row['description'];
                $image_name = $row['image_name'];

                $stmt_avg = $conn->prepare('SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_ratings FROM tbl_ratings WHERE food_id = :id');
                $stmt_avg->execute(['id' => $id]);
                $row_avg = $stmt_avg->fetch(PDO::FETCH_ASSOC);
                $avg_rating = $row_avg['total_ratings'] > 0 ? round($row_avg['avg_rating'], 1) : 0;
                $total_ratings = $row_avg['total_ratings'];
        ?>
            <div class="food-card">
                <div class="food-img">
                    <?php if ($image_name != "") { ?>
                        <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive img-curve">
                    <?php } else { echo "<div class='error'>Image not Available</div>"; } ?>
                </div>

                <div class="food-info">
                    <div class="rating-left">
                        <?php
                        if ($total_ratings > 0) {
                            $fullStars = floor($avg_rating);
                            $halfStar  = ($avg_rating - $fullStars >= 0.5) ? 1 : 0; 
                            $emptyStars = 5 - $fullStars - $halfStar;

                            for ($i=0; $i < $fullStars; $i++) { echo "<span class='star filled'>★</span>"; }
                            if ($halfStar) { echo "<span class='star half'>★</span>"; }
                            for ($i=0; $i < $emptyStars; $i++) { echo "<span class='star empty'>☆</span>"; }

                            echo " <small>($avg_rating / 5 from $total_ratings ratings)</small>";
                        } else {
                            for ($i=0; $i < 5; $i++) { echo "<span class='star no-rating'>☆</span>"; }
                            echo " <small>(No ratings yet)</small>";
                        }
                        ?>
                    </div>
                    <h4><?php echo $title; ?></h4>
                    <p class="food-price">₱<?php echo $price; ?></p>
                    <p class="food-detail"><?php echo $description; ?></p>
                </div>

                <div class="food-actions">
                    <form method="POST" action="index.php">
                        <input type="hidden" name="food_id" value="<?php echo $id; ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">🛒 Add to Cart</button>
                    </form>

                    <form method="POST" action="index.php" class="stars" title="Rate this food">
                        <input type="hidden" name="food_id" value="<?php echo $id; ?>">
                        <?php for ($r=1; $r<=5; $r++) { ?>
                            <button type="submit" name="submit_rating" value="<?php echo $r; ?>" data-tooltip="<?php echo ['Poor','Fair','Good','Very Good','Excellent'][$r-1]; ?>">⭐</button>
                        <?php } ?>
                    </form>
                </div>
            </div>
        <?php
            }
        } else {
            echo "<div class='error'>Food not available</div>";
        }
        ?>
        </div>
        <p class="text-center">
            <a href="foods.php">See All Foods</a>
        </p>
    </div>
</section>

<!-- Stats Section -->
<section class="stats">
    <div class="stats-container">
        <div class="stat-item">
            <div class="stat-number"><?php echo number_format($happy_customers); ?>+</div>
            <div class="stat-label">Happy Customers</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $menu_items; ?>+</div>
            <div class="stat-label">Menu Items</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $avg_delivery; ?></div>
            <div class="stat-label">Average Delivery</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $rating; ?></div>
            <div class="stat-label">Customer Rating</div>
        </div>
    </div>
</section>

<?php
// Dynamically pass food items to JS
$foods_js = [];
$stmt_all = $conn->query('SELECT id, title, description, image_name FROM tbl_food WHERE active=\'Yes\' ORDER BY id DESC');
$all_foods = $stmt_all->fetchAll(PDO::FETCH_ASSOC);

foreach ($all_foods as $r) {
    $img = !empty($r['image_name']) ? SITEURL . "images/food/" . $r['image_name'] : SITEURL . "images/no-image.jpg";
    $foods_js[] = [
        "id" => (int)$r['id'],
        "title" => $r['title'],
        "image" => $img,
        "description" => $r['description']
    ];
}
?>

<script>
const foodItems = <?php echo json_encode($foods_js, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT); ?>;
console.log('Loaded foodItems:', foodItems);
</script>

<?php include('partials-front/footer.php'); ?>

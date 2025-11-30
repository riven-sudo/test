<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// PDO connection (Postgres) and SITEURL should be provided in this file
include('config/constants.php'); // expects $conn (PDO) and SITEURL
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
// Happy Customers (union of orders + takeout unique customer_name)
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
$stmt_menu = $conn->query("SELECT COUNT(*) AS total_menu FROM tbl_food WHERE active = 'Yes'");
$row_menu = $stmt_menu->fetch(PDO::FETCH_ASSOC);
$menu_items = $row_menu['total_menu'] ?? 0;

// Average Delivery (in minutes) - assumes order_date exists and status 'Delivered'
$stmt_delivery = $conn->query("
    SELECT AVG(EXTRACT(EPOCH FROM (NOW() - order_date))/60) AS avg_delivery
    FROM tbl_order
    WHERE status = 'Delivered'
");
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
            <!-- Cards will be dynamically inserted here by JS -->
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
        $stmt_foods = $conn->query("SELECT * FROM tbl_food WHERE active='Yes' AND featured='Yes' LIMIT 4");
        $foods_featured = $stmt_foods->fetchAll(PDO::FETCH_ASSOC);
        if ($foods_featured) {
            foreach ($foods_featured as $row) {
                $id = $row['id'];
                $title = $row['title'];
                $price = $row['price'];
                $description = $row['description'];
                $image_name = $row['image_name'];

                // Ratings per item
                $stmt_avg = $conn->prepare('SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_ratings FROM tbl_ratings WHERE food_id = :id');
                $stmt_avg->execute(['id' => $id]);
                $row_avg = $stmt_avg->fetch(PDO::FETCH_ASSOC);
                $avg_rating = $row_avg['total_ratings'] > 0 ? round($row_avg['avg_rating'], 1) : 0;
                $total_ratings = $row_avg['total_ratings'];
        ?>
                <div class="food-card">
                    <div class="food-img">
                        <?php if ($image_name != "") { ?>
                            <img src="<?php echo SITEURL; ?>images/food/<?php echo htmlspecialchars($image_name); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="img-responsive img-curve">
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
                        <h4><?php echo htmlspecialchars($title); ?></h4>
                        <p class="food-price">₱<?php echo htmlspecialchars($price); ?></p>
                        <p class="food-detail"><?php echo htmlspecialchars($description); ?></p>
                    </div>

                    <div class="food-actions">
                        <form method="POST" action="index.php">
                            <input type="hidden" name="food_id" value="<?php echo (int)$id; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">🛒 Add to Cart</button>
                        </form>

                        <form method="POST" action="index.php" class="stars" title="Rate this food">
                            <input type="hidden" name="food_id" value="<?php echo (int)$id; ?>">
                            <?php for ($r=1; $r<=5; $r++) { ?>
                                <button type="submit" name="submit_rating" value="<?php echo $r; ?>" data-tooltip="<?php
                                    echo ($r==1?'Poor':($r==2?'Fair':($r==3?'Good':($r==4?'Very Good':'Excellent'))));
                                ?>">⭐</button>
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
// Build foods array for JS carousel (all active foods)
$foods_js = [];
$stmt_all = $conn->query("SELECT id, title, description, image_name FROM tbl_food WHERE active='Yes' ORDER BY id DESC");
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

<!doctype html>
<html lang="en">
<head>
   <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Blackstar Snackbar</title>
</head>
<body>

<!-- Hero -->
<section class="hero">
  <div class="hero-inner">
    <h1>About Blackstar Snackbar</h1> 
    <p class="lead">We craft tasty burgers, snacks, and good vibes — local flavors inspired by space (and hunger!).</p>
  </div>

  <!-- Image Carousel Card -->
  <div class="hero-card">
    <button class="carousel-btn prev">&#10094;</button>
    <div class="carousel-window">
      <div class="carousel-inner">
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
    <button class="carousel-btn next">&#10095;</button>

    <!-- Progress Dots -->
    <div class="carousel-dots">
      <span class="active"></span>
      <span></span>
      <span></span>
    </div>
  </div>

  <!-- Explore button under the carousel -->
  <a href="index.php" class="btn">Explore Menu</a>
</section>

<!-- Main -->
<main class="container" role="main">
  <section class="card">
    <h2 class="section-title">Our Story</h2>
    <p class="muted">Blackstar started as a small burger stand with one clear goal: make good food that makes people smile. Over the years we’ve grown into a neighborhood favorite while keeping the same do-it-right attitude — fresh ingredients, bold flavors, and friendly service.</p>
  </section>

  <section class="card" style="margin-top:18px;">
    <h2 class="section-title">Mission & Values</h2>
    <div class="card-row" style="margin-top:12px;">
      <div>
        <h3>Quality First</h3>
        <p class="muted">We source fresh ingredients and prepare each meal with care.</p>
      </div>
      <div>
        <h3>Community</h3>
        <p class="muted">We support local suppliers and give back via events and promos.</p>
      </div>
      <div>
        <h3>Fun</h3>
        <p class="muted">Food should bring people together — we make eating fun and memorable.</p>
      </div>
    </div>
  </section>
</main>

<!-- INTERNAL CSS (copied / merged from your original) -->
<style>
  /* ========================================
   BASE STYLES
======================================== */

    h1{ 
        text-align: center;
      }
    p {
        text-align: center;
      }
    h3 {
        text-align: center;
    }
    h2 {
        text-align: center;
    }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Arial', sans-serif; min-height: 100vh; overflow-x: hidden; }

/* VIDEO BACKGROUND */
#food-menu-section { position: relative; overflow: hidden; }
.explore-bg-video { position: absolute; top: 100px; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1; filter: brightness(100%); }

/* ALERTS */
.alert { text-align: center; padding: 12px 20px; margin: 15px auto; width: 90%; max-width: 800px; border-radius: 8px; font-size: 1rem; }
.alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert.info { background: #cce5ff; color: #004085; border: 1px solid #b8daff; }

/* EXPLORE FOOD */
.explore-food { padding: 60px 20px; position: relative; }
.carousel-title { text-align: center; font-size: clamp(2rem, 5vw, 3rem); margin-bottom: 60px; font-weight: 700; color: white; text-shadow: 2px 2px 10px rgba(0,0,0,0.3); }
.shuffle-container { position: relative; width: 100%; max-width: 1200px; height: 450px; margin: 0 auto; display: flex; align-items: center; justify-content: center; }
.card-stack { position: relative; width: 350px; height: 450px; perspective: 1000px; }
.shuffle-card { position: absolute; width: 100%; height: 100%; background: white; border-radius: 25px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55); overflow: hidden; cursor: pointer; }
.shuffle-card img { width: 100%; height: 70%; object-fit: cover; }
.shuffle-card .card-content { padding: 20px; text-align: center; background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.05)); }
.shuffle-card h3 { font-size: clamp(1.3rem, 3vw, 1.8rem); color: #333; margin-bottom: 10px; font-weight: 700; }
.shuffle-card p { color: #666; font-size: clamp(0.85rem, 2vw, 1rem); }
.carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,0.3); backdrop-filter: blur(10px); border: 2px solid rgba(255,255,255,0.5); color: white; font-size: 24px; cursor: pointer; z-index: 10; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; }
.carousel-btn:hover { background: rgba(255,255,255,0.5); transform: translateY(-50%) scale(1.1); }
.carousel-btn.left { left: 20px; }
.carousel-btn.right { right: 20px; }
.shuffle-card:nth-child(1):hover { transform: scale(1.05); box-shadow: 0 15px 50px rgba(0,0,0,0.4); }

/* STATS */
.stats { padding: 3rem 1rem; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); }
.stats-container { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; padding: 0 1rem; }
.stat-item { text-align: center; padding: 1rem; }
.stat-number { font-size: clamp(1.8rem, 4vw, 2.5rem); font-weight: bold; color: #f39c12; margin-bottom: 0.5rem; transition: transform 0.3s ease; }
.stat-item:hover .stat-number { transform: scale(1.2); color: #e67e22; }
.stat-label { color: #666; font-size: clamp(0.95rem, 2vw, 1.1rem); }

/* FOOD MENU */
.food-menu { padding: 3rem 1rem; }
.food-menu .container { max-width: 1400px; margin: 0 auto; }
.food-menu h2 { font-size: clamp(1.8rem, 4vw, 2.5rem); margin-bottom: 2rem; margin-top: 2rem; }
.food-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 20px; justify-content: center; padding: 0 10px; }
.food-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex; flex-direction: column; justify-content: space-between; align-items: center; min-height: 430px; padding: 12px; text-align: center; transition: transform 0.2s ease, box-shadow 0.2s ease; }
.food-card:hover { transform: translateY(-4px); box-shadow: 0 6px 12px rgba(0,0,0,0.15); }
.food-img { width: 100%; height: 150px; overflow: hidden; border-radius: 10px; }
.food-img img { height: 100%; object-fit: cover; }
.food-info { flex-grow: 1; display: flex; flex-direction: column; justify-content: flex-start; width: 100%; padding: 8px 0; }
.food-info h4 { font-size: clamp(0.9rem, 2vw, 1.1rem); font-weight: 600; margin: 8px 0 4px; color: #333; }
.food-price { font-size: clamp(0.9rem, 2vw, 1.1rem); font-weight: 700; color: #e74c3c; margin: 4px 0; }
.food-detail { font-size: clamp(0.75rem, 1.5vw, 0.85rem); color: #555; margin-top: 6px; line-height: 1.4; min-height: 45px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; }

/* Ratings & Stars */
.rating-left { margin-bottom: 8px; font-size: 13px; }
.star.filled { color: #f1c40f; } .star.half { color: #f1c40f; opacity: 0.6; } .star.empty { color: #ccc; } .star.no-rating { color: rgba(0,0,0,0.1); }
.rating-left small { font-size: clamp(0.7rem, 1.5vw, 0.8rem); color: #555; }
.food-actions { display: flex; flex-direction: column; align-items: center; gap: 8px; margin-top: 10px; width: 100%; }
.food-actions .btn { font-size: clamp(0.8rem, 1.5vw, 0.9rem); padding: 8px 16px; width: 100%; max-width: 200px; }

/* Stars form */
.stars { display: inline-flex; gap: 4px; margin-top: 8px; flex-wrap: wrap; justify-content: center; }
.stars button { background: none; border: none; font-size: 18px; cursor: pointer; line-height: 1; padding: 4px; transition: transform 0.2s, color 0.2s; position: relative; }
.stars button:hover { transform: scale(1.3); color: #f1c40f; }
.stars button:hover::after { content: attr(data-tooltip); position: absolute; bottom: 125%; left: 50%; transform: translateX(-50%); background: #333; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; white-space: nowrap; z-index: 10; }

/* RESPONSIVE BREAKPOINTS - (kept from original) */
@media (max-width: 768px) { .explore-food { padding: 40px 15px; } .carousel-title { margin-bottom: 40px; } .shuffle-container { height: 400px; padding: 0 60px; } .card-stack { width: 100%; max-width: 300px; height: 400px; } .carousel-btn { width: 50px; height: 50px; font-size: 20px; } .stats-container { grid-template-columns: repeat(2, 1fr); gap: 1.5rem; } .food-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; } .food-card { min-height: 400px; padding: 10px; } .food-img { height: 130px; } .stars button { font-size: 16px; } }
@media (max-width: 480px) { .explore-food { padding: 30px 10px; } .shuffle-container { height: 350px; padding: 0 50px; } .card-stack { width: 100%; max-width: 280px; height: 350px; margin: 0 auto; } .carousel-btn { width: 45px; height: 45px; font-size: 18px; background: rgba(255,255,255,0.9);} .stats-container { grid-template-columns: 1fr; gap: 1rem; } .food-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; padding: 0 5px; } .food-card { min-height: 380px; padding: 8px; } .food-img { height: 120px; } .alert { width: 95%; padding: 10px 15px; font-size: 0.9rem; } }
@media (max-width: 360px) { .food-grid { grid-template-columns: 1fr; max-width: 280px; margin: 0 auto; } .food-card { min-height: 420px; } .shuffle-container { height: 320px; padding: 0 45px; } .card-stack { width: 100%; max-width: 240px; height: 320px; } .carousel-btn { width: 38px; height: 38px; font-size: 16px; } }

/* Utility */
.text-center { text-align: center; margin-top: 2rem; }
.container { max-width: 1400px; margin: 0 auto; padding: 0 20px; }
@media (max-width: 768px) { .container { padding: 0 15px; } }
@media (max-width: 480px) { .container { padding: 0 10px; } }
</style>

<!-- JS: carousel + foodItems data -->
<script>
/* foodItems array injected from PHP */
const foodItems = <?php echo json_encode($foods_js, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT); ?>;
console.log('Loaded foodItems:', foodItems);

// CARDS / SHUFFLE logic (keeps behaviour from your original file)
let currentIndex = 0;
const cardStack = document.getElementById('cardStack');

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function initCards() {
    if (!cardStack) return;
    cardStack.innerHTML = '';
    if (!foodItems || foodItems.length === 0) {
        cardStack.innerHTML = '<div class="shuffle-card"><div class="card-content"><h3>No foods available</h3></div></div>';
        return;
    }
    foodItems.forEach((food, index) => {
        const card = document.createElement('div');
        card.className = 'shuffle-card';
        card.dataset.index = index;
        card.innerHTML = `
            <img src="${food.image}" alt="${escapeHtml(food.title)}">
            <div class="card-content">
                <h3>${escapeHtml(food.title)}</h3>
                <p>${escapeHtml(food.description || '')}</p>
            </div>
        `;
        cardStack.appendChild(card);
    });
    rotateCards('init');
}

function rotateCards(direction) {
    const cards = cardStack ? Array.from(cardStack.children) : [];
    if (cards.length === 0) return;
    if (direction === 'left') {
        currentIndex = (currentIndex + 1) % cards.length;
    } else if (direction === 'right') {
        currentIndex = (currentIndex - 1 + cards.length) % cards.length;
    } else if (direction === 'init') {
        currentIndex = 0;
    }

    cards.forEach((card, idx) => {
        const position = (idx - currentIndex + cards.length) % cards.length;
        card.style.transition = 'all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        card.style.opacity = '1';
        card.style.zIndex = '1';

        if (position === 0) {
            card.style.transform = 'translateX(0) scale(1) rotateY(0deg)';
            card.style.zIndex = '5';
            card.style.opacity = '1';
        } else if (position === 1) {
            card.style.transform = 'translateX(-280px) scale(0.9) rotateY(-15deg)';
            card.style.zIndex = '4';
            card.style.opacity = '0.85';
        } else if (position === 2) {
            card.style.transform = 'translateX(-480px) scale(0.8) rotateY(-25deg)';
            card.style.zIndex = '3';
            card.style.opacity = '0.6';
        } else if (position === cards.length - 1) {
            card.style.transform = 'translateX(280px) scale(0.9) rotateY(15deg)';
            card.style.zIndex = '4';
            card.style.opacity = '0.85';
        } else if (position === cards.length - 2) {
            card.style.transform = 'translateX(480px) scale(0.8) rotateY(25deg)';
            card.style.zIndex = '3';
            card.style.opacity = '0.6';
        } else {
            card.style.transform = 'translateX(0) scale(0.7) rotateY(0deg)';
            card.style.opacity = '0';
            card.style.zIndex = '1';
        }
    });
}

function slideLeft(){ rotateCards('left'); }
function slideRight(){ rotateCards('right'); }

// Auto-rotate
let autoRotate;
function startAutoRotate(){ autoRotate = setInterval(slideRight, 4000); }
function stopAutoRotate(){ clearInterval(autoRotate); }

document.addEventListener('DOMContentLoaded', () => {
    initCards();
    startAutoRotate();
    if (cardStack) {
        cardStack.addEventListener('mouseenter', stopAutoRotate);
        cardStack.addEventListener('mouseleave', startAutoRotate);
    }

    // small hero carousel (the three empty divs)
    const inner = document.querySelector('.carousel-inner');
    const slides = document.querySelectorAll('.carousel-inner div');
    const prevBtn = document.querySelector('.carousel-btn.prev');
    const nextBtn = document.querySelector('.carousel-btn.next');
    const dots = document.querySelectorAll('.carousel-dots span');
    let idx = 0;
    let autoSlide;

    function showSlide(i) {
      idx = (i + slides.length) % slides.length;
      if (inner) inner.style.transform = `translateX(${-idx * 100}%)`;
      dots.forEach((dot, ii)=> dot.classList.toggle('active', ii === idx));
    }
    function startAutoSlide(){ autoSlide = setInterval(()=> showSlide(idx+1), 2000); }
    function resetAutoSlide(){ clearInterval(autoSlide); startAutoSlide(); }

    if (prevBtn) prevBtn.addEventListener('click', ()=> { showSlide(idx-1); resetAutoSlide(); });
    if (nextBtn) nextBtn.addEventListener('click', ()=> { showSlide(idx+1); resetAutoSlide(); });
    dots.forEach((dot, i)=> dot.addEventListener('click', ()=> { showSlide(i); resetAutoSlide(); }));

    showSlide(0);
    startAutoSlide();
    const carousel = document.querySelector('.hero-card');
    if (carousel) {
      carousel.addEventListener('mouseenter', ()=> clearInterval(autoSlide));
      carousel.addEventListener('mouseleave', startAutoSlide);
    }
});
</script>

<?php include('partials-front/footer.php'); ?>
</body>
</html>



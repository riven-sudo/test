<?php 
include('partials-front/menu.php'); 

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

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
                'id'    => $food['id'],
                'title' => $food['title'],
                'price' => $food['price'],
                'qty'   => 1,
                'image' => $food['image_name']
            ];
        }
    }
}
?>

<style>
/* ===== Grid Card Layout Styling ===== */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f2f2f2;
    margin: 0;
    padding: 0;
}

.food-menu {
    padding: 40px 0;
    background-color: #f2f2f2;
}

.food-menu h2 {
    text-align: center;
    color: #f39c12;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 30px;
}

/* Grid container */
.food-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
    justify-items: center;
    width: 90%;
    margin: 0 auto;
}

/* Card styling */
.food-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    text-align: center;
    transition: all 0.3s ease;
    width: 100%;
    max-width: 280px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.food-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Image styling */
.food-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}

/* Title, price, and description */
.food-card h4 {
    margin: 10px 0 5px;
    font-size: 20px;
    color: #333;
}

.food-card .food-price {
    font-size: 18px;
    color: #e67e22;
    font-weight: 600;
    margin-bottom: 10px;
}

.food-card p {
    font-size: 14px;
    color: #666;
    padding: 0 15px;
    flex-grow: 1;
}

/* Add to cart button */
.food-card form {
    padding: 15px 0;
}

.food-card .btn {
    background-color: #f39c12;
    border: none;
    color: #fff;
    padding: 8px 18px;
    border-radius: 25px;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.3s;
}

.food-card .btn:hover {
    background-color: #e67e22;
}

.error {
    text-align: center;
    color: #e74c3c;
    font-weight: 500;
    margin-top: 15px;
}
</style>

<!-- fOOD sEARCH Section -->
<section class="food-search text-center">
    <div class="container">
        <?php
        $search = "";
        $search_alt = "";
        $category_food_ids = [];

        if (isset($_POST['search']) && trim($_POST['search']) !== "") {
            $search = strtolower(mysqli_real_escape_string($conn, $_POST['search']));

            if (substr($search, -1) === 's') {
                $search_alt = substr($search, 0, -1);
            } else {
                $search_alt = $search . 's';
            }

            $sql_cat = "SELECT id FROM tbl_category WHERE LOWER(title) LIKE '%$search%'";
            $res_cat = mysqli_query($conn, $sql_cat);

            if ($res_cat && mysqli_num_rows($res_cat) > 0) {
                while ($row_cat = mysqli_fetch_assoc($res_cat)) {
                    $category_food_ids[] = $row_cat['id'];
                }
            }
        }
        ?>

        <?php if (!empty($search)) : ?>
            <h2>Foods on Your Search <a href="#" class="text-white">"<?php echo htmlspecialchars($search); ?>"</a></h2>
        <?php else: ?>
            <h2>Please enter a keyword to search for food.</h2>
        <?php endif; ?>
    </div>
</section>

<!-- Food Menu Section -->
<section class="food-menu">
    <div class="container">
        <h2>Food Menu</h2>

        <div class="food-grid">
        <?php
        if (!empty($search)) {
            $sql = "SELECT * FROM tbl_food 
                WHERE LOWER(title) LIKE '%$search%' 
                OR LOWER(description) LIKE '%$search%' 
                OR LOWER(title) LIKE '%$search_alt%' 
                OR LOWER(description) LIKE '%$search_alt%'";

            if(!empty($category_food_ids)) {
                $ids = implode(',', $category_food_ids);
                $sql .= " OR category_id IN ($ids)";
            }

            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);

            if($count > 0) {
                while($row = mysqli_fetch_assoc($res)) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $price = $row['price'];
                    $description = $row['description'];
                    $image_name = $row['image_name'];
                    ?>

                    <div class="food-card">
                        <?php if($image_name=="") : ?>
                            <div class='error'>Image not available</div>
                        <?php else: ?>
                            <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo htmlspecialchars($title); ?>">
                        <?php endif; ?>

                        <h4><?php echo htmlspecialchars($title); ?></h4>
                        <p class="food-price">₱<?php echo number_format($price,2); ?></p>
                        <p><?php echo htmlspecialchars($description); ?></p>

                        <form method="POST" action="food-search.php">
                            <input type="hidden" name="food_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                        </form>
                    </div>

                    <?php
                }
            } else {
                echo "<div class='error'>We couldn’t find any matching food. Did you type it correctly?</div>";
            }
        }
        ?>
        </div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>

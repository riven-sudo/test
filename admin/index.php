<?php include('partials/menu.php'); ?>

<!--Main Content Section Starts-->
<div class="main-content">
    <div class="wrapper">
        <h1 class="dashboard-title">Dashboard</h1>

        <?php
            if(isset($_SESSION['login'])) {
                echo '<div class="login-msg">'.$_SESSION['login'].'</div>';
                unset($_SESSION['login']);
            }
        ?>

        <!-- Stats Cards -->
        <div class="dashboard-cards">
            <!-- Categories Card -->
            <div class="card" onclick="window.location.href='manage-category.php'">
                <?php
                    $sql = "SELECT * FROM tbl_category";
                    $res = mysqli_query($conn, $sql);
                    $count = mysqli_num_rows($res);
                ?>
                <div class="card-content">
                    <h2><?php echo $count; ?></h2>
                    <p>Categories</p>
                </div>
            </div>

            <!-- Foods Card -->
            <div class="card" onclick="window.location.href='manage-food.php'">
                <?php
                    $sql2 = "SELECT * FROM tbl_food";
                    $res2 = mysqli_query($conn, $sql2);
                    $count2 = mysqli_num_rows($res2);
                ?>
                <div class="card-content">
                    <h2><?php echo $count2; ?></h2>
                    <p>Foods</p>
                </div>
            </div>

            <!-- Take-out Orders Card -->
            <div class="card card-orange" onclick="window.location.href='manage-order-dinein.php'">
                <?php
                    $sql_dinein = "SELECT * FROM tbl_order WHERE order_type='Take-out'";
                    $res_dinein = mysqli_query($conn, $sql_dinein);
                    $count_dinein = mysqli_num_rows($res_dinein);
                ?>
                <div class="card-content">
                    <h2><?php echo $count_dinein; ?></h2>
                    <p>Take-out Orders</p>
                </div>
            </div>

            <!-- Delivery Orders Card -->
            <div class="card card-red" onclick="window.location.href='manage-order-takeout.php'">
                <?php
                    $sql_takeout = "SELECT * FROM tbl_takeout WHERE order_type='Takeout'";
                    $res_takeout = mysqli_query($conn, $sql_takeout);
                    $count_takeout = mysqli_num_rows($res_takeout);
                ?>
                <div class="card-content">
                    <h2><?php echo $count_takeout; ?></h2>
                    <p>Delivery Orders</p>
                </div>
            </div>

            <!-- Total Orders Card -->
            <div class="card card-blue">
                <?php
                    $sql_dinein_orders = "SELECT * FROM tbl_order";
                    $res_dinein_orders = mysqli_query($conn, $sql_dinein_orders);
                    $count_dinein_orders = mysqli_num_rows($res_dinein_orders);

                    $sql_takeout_orders = "SELECT * FROM tbl_takeout";
                    $res_takeout_orders = mysqli_query($conn, $sql_takeout_orders);
                    $count_takeout_orders = mysqli_num_rows($res_takeout_orders);

                    $total_orders = $count_dinein_orders + $count_takeout_orders;
                ?>
                <div class="card-content">
                    <h2><?php echo $total_orders; ?></h2>
                    <p>Total Orders</p>
                </div>
            </div>

            <!-- Total Revenue Card -->
            <div class="card card-green">
                <?php
                    // Separate revenue
                    $sql_takeout_revenue = "SELECT SUM(total) AS Total FROM tbl_order WHERE order_type='Take-out' AND status='Take-out'";
                    $res_takeout_revenue = mysqli_query($conn, $sql_takeout_revenue);
                    $row_takeout_revenue = mysqli_fetch_assoc($res_takeout_revenue);
                    $revenue_takeout = $row_takeout_revenue['Total'] ?? 0;

                    $sql_delivery_revenue = "SELECT SUM(total) AS Total FROM tbl_takeout WHERE order_type='Takeout' AND status IN ('Ordered','Delivered')";
                    $res_delivery_revenue = mysqli_query($conn, $sql_delivery_revenue);
                    $row_delivery_revenue = mysqli_fetch_assoc($res_delivery_revenue);
                    $revenue_delivery = $row_delivery_revenue['Total'] ?? 0;

                    $total_revenue = $revenue_takeout + $revenue_delivery;
                ?>
                <div class="card-content">
                    <h2>₱<?php echo number_format($total_revenue,2); ?></h2>
                    <p>Total Revenue</p>
                    <small>Take-out: ₱<?php echo number_format($revenue_takeout,2); ?><br>
                           Delivery: ₱<?php echo number_format($revenue_delivery,2); ?></small>
                </div>
            </div>
        </div>

        <!-- Analytics -->
        <h2 class="analytics-title">Analytics</h2>
        <div class="analytics-charts">
            <div class="chart-container">
                <canvas id="ordersChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</div>
<!--Main Content Section Ends-->

<?php include('partials/footer.php') ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Orders Pie Chart
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ordersCtx, {
        type: 'pie',
        data: {
            labels: ['Take-out Orders', 'Delivery Orders'],
            datasets: [{
                data: [<?php echo $count_dinein_orders; ?>, <?php echo $count_takeout_orders; ?>],
                backgroundColor: ['#FF9800', '#FF6B00']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Orders Analytics' }
            }
        }
    });

    // Revenue Bar Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: ['Take-out', 'Delivery'],
            datasets: [{
                label: 'Revenue (₱)',
                data: [<?php echo $revenue_takeout; ?>, <?php echo $revenue_delivery; ?>],
                backgroundColor: ['#FF9800', '#FF6B00']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Revenue Analytics' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.raw.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
                        }
                    }
                }
            }
        }
    });
</script>

<style>
/* ================== General Styles ================== */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f9f9f9;
    padding-top: 100px;
    color: #333;
}

.dashboard-title {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.login-msg {
    padding: 10px 15px;
    background: #e0ffe0;
    border-left: 5px solid #4caf50;
    margin-bottom: 20px;
    border-radius: 4px;
}

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 cards per row */
    gap: 25px; /* space between cards */
    justify-items: center;
}
/* Card Styles */
.card {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    width: 100%;
    max-width: 320px; /* increase max-width to make cards wider */
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.card h2 {
    font-size: 2rem;
    margin-bottom: 5px;
}

.card p {
    font-weight: 500;
    color: #555;
}

.card small {
    display: block;
    margin-top: 10px;
    color: #777;
   
}



/* Colored Cards */
.card-orange { background: #fff3e0; }
.card-red { background: #ffe0e0; }
.card-blue { background: #e0f0ff; }
.card-green { background: #e0ffe0; }

/* Analytics Styles */
.analytics-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 40px 0 20px;
    text-align: center;
}

.analytics-charts {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 40px;
}

.chart-container {
    flex: 1;
    min-width: 300px;
    max-width: 500px;
}

/* Responsive Adjustments */
@media(max-width: 768px) {
    .dashboard-cards {
        justify-content: center;
    }
}

@media(max-width: 1024px) {
    .dashboard-cards {
        grid-template-columns: repeat(2, 1fr); /* 2 cards per row on medium screens */
    }
}

@media(max-width: 600px) {
    .dashboard-cards {
        grid-template-columns: 1fr; /* 1 card per row on small screens */
    }
}
 
</style>

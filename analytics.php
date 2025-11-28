<?php include('partials-front/menu.php'); ?>

<section class="analytics">
    <div class="container">
        <h2 class="text-center">📊 Analytics Dashboard (Demo)</h2>

        <div class="chart-grid">
            <div class="chart-box">
                <canvas id="searchChart"></canvas>
            </div>
            <div class="chart-box">
                <canvas id="cartChart"></canvas>
            </div>
            <div class="chart-box full-width">
                <canvas id="ratingChart"></canvas>
            </div>
        </div>
    </div>
</section>

<style>
.chart-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    justify-content: center;
    align-items: center;
}
.chart-box {
    background: #fff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    height: 250px; /* smaller height */
}
.chart-box.full-width {
    grid-column: span 2; /* line chart takes full row */
    height: 220px;
}
canvas {
    width: 100% !important;
    height: 100% !important;
}
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ===== Dummy Data (replace with DB values later) =====
const topSearches = ["Pizza", "Burger", "Pasta", "Fries", "Chicken"];
const searchCounts = [120, 95, 80, 60, 45];

const topFoods = ["Hawaiian Pizza", "Cheese Burger", "Carbonara", "French Fries"];
const cartCounts = [90, 70, 50, 40];

const foodRatings = ["Pizza", "Burger", "Pasta", "Fries"];
const avgRatings = [4.5, 4.2, 3.8, 4.0];

// ===== Search Chart =====
new Chart(document.getElementById("searchChart"), {
    type: "bar",
    data: {
        labels: topSearches,
        datasets: [{
            label: "Top Searches",
            data: searchCounts,
            backgroundColor: "rgba(54, 162, 235, 0.6)"
        }]
    },
    options: { plugins: { legend: { display: false } } }
});

// ===== Cart Chart =====
new Chart(document.getElementById("cartChart"), {
    type: "pie",
    data: {
        labels: topFoods,
        datasets: [{
            label: "Most Added to Cart",
            data: cartCounts,
            backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0"]
        }]
    }
});

// ===== Ratings Chart =====
new Chart(document.getElementById("ratingChart"), {
    type: "line",
    data: {
        labels: foodRatings,
        datasets: [{
            label: "Average Ratings",
            data: avgRatings,
            fill: false,
            borderColor: "rgba(75,192,192,1)",
            tension: 0.1
        }]
    }
});
</script>

<?php include('partials-front/footer.php'); ?>

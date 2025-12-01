<?php
// ====================================================
// Start output buffering FIRST → prevents header errors
// ====================================================
if (!defined('OUTPUT_BUFFERING_STARTED')) {
    define('OUTPUT_BUFFERING_STARTED', true);
    ob_start();
}

// ====================================================
// Prevent duplicate loading
// ====================================================
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

    // Start session safely
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Load env variables
    $host = getenv("MYSQL_HOST");
    $port = getenv("MYSQL_PORT") ?: 3306; // default MySQL port
    $user = getenv("MYSQL_USER");
    $pass = getenv("MYSQL_PASS");
    $db   = getenv("MYSQL_DB");

    if (!$host || !$user || !$db) {
        error_log("Missing environment variables.");
        exit("Database environment variables are missing.");
    }

    // Connect
    $conn = mysqli_connect($host, $user, $pass, $db, $port);
    if (!$conn) {
        error_log("MySQL Connection Error: " . mysqli_connect_error());
        exit("Database connection failed.");
    }

    // Define URL once
    if (!defined('SITEURL')) {
        define('SITEURL', 'https://test-1-v6th.onrender.com/');
    }

    // Initialize cart if not set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}
?>

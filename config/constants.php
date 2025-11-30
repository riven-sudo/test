<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables
$db_host     = getenv('DB_HOST');     // e.g., dpg-d4kint49c44c73evteu0-a
$db_user     = getenv('DB_USER'); // e.g., food_order_13bv_user
$db_pass     = getenv('DB_PASSWORD'); // your DB password
$db_name     = getenv('DB_NAME');     // e.g., food_order_13bv

// Define SITEURL (adjust if needed)
define('SITEURL', 'https://test-1-v6th.onrender.com/');

try {
    // Create PDO connection
    $dsn = "pgsql:host=$db_host;dbname=$db_name";
    $conn = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // OPTIONAL: remove after testing
    // echo "Connected successfully to PostgreSQL!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


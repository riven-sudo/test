<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables
$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASSWORD');
$db_name = getenv('DB_NAME');
$db_port = getenv('DB_PORT') ?: 5432; // default to 5432 just in case

// Define SITEURL (adjust if needed)
define('SITEURL', 'https://test-1-v6th.onrender.com/');

try {
    // Create PDO connection
    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
    $conn = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // echo "Connected successfully to PostgreSQL!"; // optional
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

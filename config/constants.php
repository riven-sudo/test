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
$db_name = getenv('DB_DATABASE');

// Debug: show which env variables are loaded
echo "<h3>Database Environment Variables:</h3>";
echo "DB_HOST: " . ($db_host ? $db_host : "<span style='color:red'>NOT SET</span>") . "<br>";
echo "DB_USER: " . ($db_user ? $db_user : "<span style='color:red'>NOT SET</span>") . "<br>";
echo "DB_PASSWORD: " . ($db_pass ? '******' : "<span style='color:red'>NOT SET</span>") . "<br>";
echo "DB_DATABASE: " . ($db_name ? $db_name : "<span style='color:red'>NOT SET</span>") . "<br>";

// Check if any variable is missing
if (!$db_host || !$db_user || !$db_pass || !$db_name) {
    die("<span style='color:red'>Error: One or more database environment variables are missing!</span>");
}

// Define SITEURL
define('SITEURL', 'https://test-1-v6th.onrender.com/');

try {
    // DSN without port
    $dsn = "pgsql:host=$db_host;dbname=$db_name";
    $conn = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<span style='color:green'>Database connection successful!</span>";
} catch (PDOException $e) {
    echo "<span style='color:red'>Database connection failed:</span> " . $e->getMessage();
}

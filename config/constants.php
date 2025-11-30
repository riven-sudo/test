<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables
$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');        // corrected
$db_pass = getenv('DB_PASSWORD');
$db_name = getenv('DB_DATABASE');    // corrected

// Check if environment variables are loaded
if (!$db_host || !$db_user || !$db_pass || !$db_name) {
    die("One or more database environment variables are missing.");
}

// Define SITEURL
define('SITEURL', 'https://test-1-v6th.onrender.com/');

try {
    $dsn = "pgsql:host=$db_host;dbname=$db_name"; // port removed
    $conn = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

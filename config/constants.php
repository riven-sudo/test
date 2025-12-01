<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load MySQL environment variables
$host = getenv("MYSQLHOST");
$port = getenv("MYSQLPORT");
$user = getenv("MYSQLUSER");
$pass = getenv("MYSQLPASSWORD");
$db   = getenv("MYSQLDATABASE");

// Validate
if (!$host || !$user || !$db) {
    die("Missing required database environment variables.");
}

// Connect
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    error_log("MySQL Connection Error: " . mysqli_connect_error());
    die("Database connection failed.");
}

// No echo here — keep silent for production
define('SITEURL', 'https://test-1-v6th.onrender.com/');
?>


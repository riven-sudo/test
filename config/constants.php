<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load MySQL environment variables
$host = getenv("MYSQL_HOST");
$port = getenv("MYSQL_PORT");
$user = getenv("MYSQL_USER");
$pass = getenv("MYSQL_PASS");
$db   = getenv("MYSQL_DB");

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

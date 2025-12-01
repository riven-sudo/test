<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables
$host = getenv("MYSQL_HOST");
$port = getenv("MYSQL_PORT");
$user = getenv("MYSQL_USER");
$pass = getenv("MYSQL_PASS"); // Sensitive, do NOT print
$db   = getenv("MYSQL_DB");

// Optional: debug only safe info
error_log("DEBUG: Host=$host, Port=$port, User=$user, DB=$db");

// Connect to MySQL
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    // Log the error and show friendly message
    error_log("MySQL Connection Error: " . mysqli_connect_error());
    die("Database connection failed. Please check logs.");
}

// Connection successful
error_log("MySQL Connected Successfully!");

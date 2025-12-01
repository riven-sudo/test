<?php
// Prevent duplicate loading
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

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

    // Start session (only once)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Define SITEURL only once
    if (!defined('SITEURL')) {
        define('SITEURL', 'https://test-1-v6th.onrender.com/');
    }
}
?>



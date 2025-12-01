<?php
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

    // Load MySQL environment variables
    $host = getenv("MYSQL_HOST");
    $port = getenv("MYSQL_PORT");
    $user = getenv("MYSQL_USER");
    $pass = getenv("MYSQL_PASS");
    $db   = getenv("MYSQL_DB");

    // Validate environment variables early
    if (!$host || !$user || !$db) {
        // DO NOT echo or die() with HTML → this prints output
        error_log("Missing environment variables.");
        exit;
    }

    // Connect to DB
    $conn = mysqli_connect($host, $user, $pass, $db, $port);
    if (!$conn) {
        // DO NOT echo errors
        error_log("MySQL Connection Error: " . mysqli_connect_error());
        exit;
    }

    // Start session BEFORE any include prints output
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!defined('SITEURL')) {
        define('SITEURL', 'https://test-1-v6th.onrender.com/');
    }
}

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

    // Load env variables
    $host = getenv("MYSQL_HOST");
    $port = getenv("MYSQL_PORT");
    $user = getenv("MYSQL_USER");
    $pass = getenv("MYSQL_PASS");
    $db   = getenv("MYSQL_DB");

    if (!$host || !$user || !$db) {
        error_log("Missing environment variables.");
        exit;
    }

    // Connect
    $conn = mysqli_connect($host, $user, $pass, $db, $port);

    if (!$conn) {
        error_log("MySQL Connection Error: " . mysqli_connect_error());
        exit;
    }

    // Start session safely
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Define URL once
    if (!defined('SITEURL')) {
        define('SITEURL', 'https://test-1-v6th.onrender.com/');
    }
}

// DO NOT ob_end_clean() → this would delete output & break sessions
?>

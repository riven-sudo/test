<?php
// ====================================================
// Start output buffering BEFORE anything else to prevent
// "headers already sent" errors from BOM or stray whitespace
// ====================================================
if (!defined('OUTPUT_BUFFERING_STARTED')) {
    define('OUTPUT_BUFFERING_STARTED', true);
    if (ob_get_level() === 0) {
        ob_start();
    }
}

// ====================================================
// Start session (after buffering started)
// ====================================================
if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        session_start();
    } else {
        // If headers were already sent, ensure buffering and try to start session
        if (ob_get_level() === 0) {
            ob_start();
        }
        @session_start();
    }
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

    // Define URL once
    if (!defined('SITEURL')) {
        define('SITEURL', 'https://test-1-v6th.onrender.com/');
    }
}

// DO NOT ob_end_clean() → this would delete output & break sessions
?>

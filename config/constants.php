<?php
// ====================================================
// Start output buffering BEFORE anything else
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

    // Load audit helper (POC)
    require_once __DIR__ . '/audit.php';

    // ====================================================
    // Auto-detect SITE URL (supports 2 URLs)
    // ====================================================
    if (!defined('SITEURL')) {
        $domain = $_SERVER['HTTP_HOST'];

        if ($domain === "test-1-v6th.onrender.com") {
            define("SITEURL", "https://test-1-v6th.onrender.com/");
        }
        else if ($domain === "test-06s7.onrender.com") {
            define("SITEURL", "https://test-06s7.onrender.com/");
        }
        else {
            define("SITEURL", "https://" . $domain . "/");
        }
    }
} // ← THIS closing brace was missing

// DO NOT ob_end_clean() → this would delete output & break sessions
?>

<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable full error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -----------------------------
// Environment Variables (Render)
// -----------------------------
$SITEURL      = getenv('SITEURL') ?: 'http://localhost/blackstar/';
$DB_HOST      = getenv('DB_HOST') ?: 'localhost';
$DB_USER      = getenv('DB_USER') ?: 'root';
$DB_PASSWORD  = getenv('DB_PASSWORD') ?: '';
$DB_NAME      = getenv('DB_NAME') ?: 'food-order';

// Define constants
define('SITEURL', $SITEURL);
define('LOCALHOST', $DB_HOST);
define('DB_USERNAME', $DB_USER);
define('DB_PASSWORD', $DB_PASSWORD);
define('DB_NAME', $DB_NAME);

// -----------------------------
// Database Connection with Debug
// -----------------------------
$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (!$conn) {
    die("
        ❌ Database Connection Failed! <br>
        Host: $DB_HOST <br>
        User: $DB_USER <br>
        Password: " . ($DB_PASSWORD ? 'SET' : 'EMPTY') . "<br>
        Database: $DB_NAME <br>
        Error: " . mysqli_connect_error() . "
    ");
} else {
    echo "✅ Database Connected Successfully! <br>";
}
?>

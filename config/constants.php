<?php
// Start Session kung wala pa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create Constants to store Non repeating values
if (!defined('SITEURL')) {
    define('SITEURL', 'http://localhost/blackstar/');
}

if (!defined('LOCALHOST')) {
    define('LOCALHOST', 'localhost');
}

if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'root');
}

if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'food-order');
}

// Database Connection
$conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD) or die(mysqli_error($conn));
$db_select = mysqli_select_db($conn, DB_NAME) or die(mysqli_error($conn));

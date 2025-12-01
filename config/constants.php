<?php
// Enable PHP errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables from Render
$host = getenv("MYSQL_HOST");
$port = getenv("MYSQL_PORT");
$user = getenv("MYSQL_USER");
$pass = getenv("MYSQL_PASS"); // Sensitive, do not echo
$db   = getenv("MYSQL_DB");

// Debug non-sensitive info only
echo "DEBUG: ENV variables loaded<br>";
echo "Host: $host<br>";
echo "Port: $port<br>";
echo "User: $user<br>";
echo "DB: $db<br>";

// Connect to MySQL
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "MySQL Connected Successfully!";
?>

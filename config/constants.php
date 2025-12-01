<?php
// Enable debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables from Render
$host = getenv("MYSQL_HOST");
$user = getenv("MYSQL_USER");
$pass = getenv("MYSQL_PASS");
$db   = getenv("MYSQL_DB");
$port = getenv("MYSQL_PORT");

// Debug output (you can remove later)
echo "DEBUG: Loaded ENV variables<br>";
echo "Host: $host<br>";
echo "User: $user<br>";
echo "DB: $db<br>";

// Create connection
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "MySQL Connected Successfully!";
}
?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables
$host = getenv("MYSQL_HOST");
$port = getenv("MYSQL_PORT");
$user = getenv("MYSQL_USER");
$pass = getenv("MYSQL_PASS"); // DO NOT print
$db   = getenv("MYSQL_DB");

// Debug only safe info
echo "DEBUG: Host: $host<br>";
echo "DEBUG: Port: $port<br>";
echo "DEBUG: User: $user<br>";
echo "DEBUG: DB: $db<br>";

// Connect to MySQL (pass password directly)
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "MySQL Connected Successfully!";
?>

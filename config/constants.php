<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ---------------------------------------------------
// ENV LOADER (Works for Render): getenv + $_ENV + $_SERVER
// ---------------------------------------------------
function env($key) {
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    $v = getenv($key);
    return $v !== false ? $v : null;
}

// ---------------------------------------------------
// Render provides a full DATABASE_URL
// Example: postgres://user:password@host:port/dbname
// ---------------------------------------------------
$db_url = env('DATABASE_URL');

if (!$db_url) {
    die('<strong style="color:red">ERROR: DATABASE_URL is not set in environment!</strong>');
}

// Parse DATABASE_URL
$parts = parse_url($db_url);
$db_host = $parts['host'];
$db_port = $parts['port'] ?? 5432;
$db_user = $parts['user'];
$db_pass = $parts['pass'];
$db_name = ltrim($parts['path'], '/');

// ---------------------------------------------------
// DEBUG SECTION
// ---------------------------------------------------
echo "<h3>Database Environment Variables:</h3>";
echo "DB_HOST: $db_host<br>";
echo "DB_USER: $db_user<br>";
echo "DB_PASSWORD: " . ($db_pass ? '******' : '<span style="color:red">NOT SET</span>') . "<br>";
echo "DB_DATABASE: $db_name<br><br>";

// ---------------------------------------------------
// Constants
// ---------------------------------------------------
define('SITEURL', 'https://test-1-v6th.onrender.com/');

// ---------------------------------------------------
// DATABASE CONNECTION (PostgreSQL via PDO)
// ---------------------------------------------------
try {
    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";

    $conn = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "<span style='color:green'>Database connection successful!</span><br>";
} catch (PDOException $e) {
    echo "<span style='color:red'>Database connection failed:</span> " . $e->getMessage();
    die();
}
?>

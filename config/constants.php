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
if (!function_exists('env')) {
    function env($key) {
        if (isset($_ENV[$key])) return $_ENV[$key];
        if (isset($_SERVER[$key])) return $_SERVER[$key];
        $v = getenv($key);
        return $v !== false ? $v : null;
    }
}

// ---------------------------------------------------
// DATABASE CONFIG FROM DATABASE_URL
// DATABASE_URL example:
// postgres://username:password@hostname:5432/dbname
// ---------------------------------------------------
$databaseUrl = env('DATABASE_URL');

if ($databaseUrl) {
    $components = parse_url($databaseUrl);

    $db_host = $components['host'] ?? null;
    $db_user = $components['user'] ?? null;
    $db_pass = $components['pass'] ?? null;
    $db_name = isset($components['path']) ? ltrim($components['path'], '/') : null;
} else {
    $db_host = $db_user = $db_pass = $db_name = null;
}

// ---------------------------------------------------
// DEBUG SECTION
// ---------------------------------------------------
echo "<h3>Database Environment Variables:</h3>";
echo "DB_HOST: " . ($db_host ?: "<span style='color:red'>NOT SET</span>") . "<br>";
echo "DB_USER: " . ($db_user ?: "<span style='color:red'>NOT SET</span>") . "<br>";
echo "DB_PASSWORD: " . ($db_pass ? '******' : "<span style='color:red'>NOT SET</span>") . "<br>";
echo "DB_DATABASE: " . ($db_name ?: "<span style='color:red'>NOT SET</span>") . "<br><br>";

// ---------------------------------------------------
// STOP EXECUTION IF ANY REQUIRED VALUE IS MISSING
// ---------------------------------------------------
if (!$db_host || !$db_user || !$db_pass || !$db_name) {
    echo "<hr><strong style='color:red'>ERROR: Missing database environment variables!</strong><br><br>";
    die("Fix: Add DATABASE_URL in Render Dashboard → Environment and Redeploy.");
}

// ---------------------------------------------------
// Constants
// ---------------------------------------------------
define('SITEURL', 'https://test-1-v6th.onrender.com/');

// ---------------------------------------------------
// DATABASE CONNECTION (PostgreSQL via PDO)
// ---------------------------------------------------
try {
    $dsn = "pgsql:host=$db_host;dbname=$db_name";

    $conn = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "<span style='color:green'>Database connection successful!</span><br>";
} catch (PDOException $e) {
    echo "<span style='color:red'>Database connection failed:</span> " . $e->getMessage();
    die();
}
?>

<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting (you can disable on production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ---------------------------------------------------
// ENV LOADER
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
// ---------------------------------------------------
$databaseUrl = env('DATABASE_URL');

if ($databaseUrl) {
    $components = parse_url($databaseUrl);

    $db_host = $components['host'] ?? null;
    $db_user = $components['user'] ?? null;
    $db_pass = $components['pass'] ?? null;
    $db_name = isset($components['path']) ? ltrim($components['path'], '/') : null;
} else {
    die("DATABASE_URL not found. Add it inside Render → Environment.");
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
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false
    ]);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

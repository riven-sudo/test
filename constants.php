<?php
// Load environment variables
$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USERNAME');
$db_pass = getenv('DB_PASSWORD');
$db_name = getenv('DB_NAME');

// Build DSN string for PostgreSQL
$dsn = "pgsql:host=$db_host;dbname=$db_name";

try {
    // Create PDO connection
    $conn = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // OPTIONAL: remove this after testing
    // echo "Connected successfully!";

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

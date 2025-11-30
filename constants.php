<?php
// Load environment variables
$db_host = getenv('dpg-d4kint49c44c73evteu0-a');
$db_user = getenv('food_order_13bv_user');
$db_pass = getenv('lo7PgG7E2KRTxxgGkbYhDCWgjXLAXgeO');
$db_name = getenv('food_order_13bv');

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

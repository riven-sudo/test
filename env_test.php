<?php
// env_test.php — temp file for verification
header('Content-Type: text/plain');
echo "---- \$_ENV ----\n";
var_dump($_ENV);
echo "\n---- getenv() ----\n";
echo "DB_HOST: " . var_export(getenv('DB_HOST'), true) . "\n";
echo "DB_USER: " . var_export(getenv('DB_USER'), true) . "\n";
echo "DB_PASSWORD: " . var_export(getenv('DB_PASSWORD'), true) . "\n";
echo "DB_DATABASE: " . var_export(getenv('DB_DATABASE'), true) . "\n";

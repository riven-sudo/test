<?php
require_once __DIR__ . '/../config/constants.php';

// Simple test - requires DB to be set up and connection working
Audit::log('poc_test', 'test', '1', null, array('status' => 'ok'), array('note' => 'POC entry'));

$rows = Audit::fetchRecent(5);
foreach ($rows as $r) {
    echo $r['id'] . ' | ' . $r['created_at'] . ' | ' . $r['action'] . ' | ' . $r['username'] . "\n";
}

echo "Done\n";
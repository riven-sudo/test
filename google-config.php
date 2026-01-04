<?php
require_once 'vendor/autoload.php';

// Load environment variables (Render automatically injects them)
$googleClientId = getenv('GOOGLE_CLIENT_ID');
$googleClientSecret = getenv('GOOGLE_CLIENT_SECRET');

// Safety checks
if (!$googleClientId || !$googleClientSecret) {
    die("Google OAuth is not configured. Missing environment variables.");
}

$client = new Google_Client();
$client->setClientId($googleClientId);
$client->setClientSecret($googleClientSecret);
$client->setRedirectUri("https://test-1-gen7.onrender.com/google-callback.php");
$client->addScope("email");
$client->addScope("profile");

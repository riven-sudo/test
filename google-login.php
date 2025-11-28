<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId("859306833735-qlj4iolrfnkhvihpuc856njmej0oivao.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("https://a85282f07a4180.lhr.life/blackstar/google-callback.php");
$client->addScope("email");
$client->addScope("profile");

header("Location: " . $client->createAuthUrl());
exit;

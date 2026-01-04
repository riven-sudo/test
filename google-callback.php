<?php
session_start();

// Suppress deprecation warnings from Google API client (PHP 8.2+)
error_reporting(E_ALL & ~E_DEPRECATED);

require 'google-config.php'; // contains $client
include 'config/constants.php'; // contains $conn (mysqli connection)

if (!isset($_GET['code'])) {
    die("Login failed: no code returned.");
}

try {
    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        echo "<h3>Google Login failed</h3>";
        echo "<pre>"; print_r($token); echo "</pre>";
        exit;
    }

    $client->setAccessToken($token);

    // Get user info
    $googleService = new Google_Service_Oauth2($client);
    $googleUser = $googleService->userinfo->get();

    $google_id = $googleUser['id'];
    $name      = $googleUser['name'];
    $email     = $googleUser['email'];
    $picture   = $googleUser['picture'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM tbl_customer WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Create a dummy password for Google users
        $dummy_password = bin2hex(random_bytes(16));

        // Insert new Google user
        $stmt = $conn->prepare("
            INSERT INTO tbl_customer 
                (username, email, google_id, profile_pic, password, status, is_member, discount) 
            VALUES (?, ?, ?, ?, ?, 'Approved', 1, 5)
        ");
        $stmt->bind_param("sssss", $name, $email, $google_id, $picture, $dummy_password);
        $stmt->execute();

        $customer_id = $stmt->insert_id;
    } else {
        $row = $result->fetch_assoc();
        $customer_id = $row['id'];
        $name = $row['username']; // use DB username if exists
    }

    // Set session variables
    $_SESSION['customer'] = $name;
    $_SESSION['customer_id'] = $customer_id;

    // Redirect to homepage
    header("Location: index.php");
    exit;

} catch (Exception $e) {
    echo "Error during Google login: " . $e->getMessage();
    exit;
}

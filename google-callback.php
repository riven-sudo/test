<?php
session_start();
require 'google-config.php';
include 'config/constants.php';

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        echo "<h3>Google Login failed</h3>";
        echo "<pre>"; print_r($token); echo "</pre>";
        exit;
    }

    $client->setAccessToken($token);

    $google_service = new Google_Service_Oauth2($client);
    $google_user = $google_service->userinfo->get();

    $google_id = $google_user['id'];
    $name      = $google_user['name'];
    $email     = $google_user['email'];
    $picture   = $google_user['picture'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM tbl_customer WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Insert new Google user (auto-approved with 5% discount)
        $stmt = $conn->prepare("
            INSERT INTO tbl_customer (username, email, google_id, profile_pic, status, is_member, discount) 
            VALUES (?, ?, ?, ?, 'Approved', 1, 5)
        ");
        $stmt->bind_param("ssss", $name, $email, $google_id, $picture);
        $stmt->execute();

        // Get inserted ID
        $customer_id = $stmt->insert_id;
    } else {
        $row = $result->fetch_assoc();
        $customer_id = $row['id'];
        $name = $row['username']; // use DB username if exists
    }

    // Set session like process-login.php
    $_SESSION['customer'] = $name;
    $_SESSION['customer_id'] = $customer_id;

    header("Location: index.php");
    exit;
} else {
    echo "Login failed. No code returned.";
}

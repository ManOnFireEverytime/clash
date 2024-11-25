<?php
header('Access-Control-Allow-Origin: *');  // Allow any origin (or specify your frontend URL)
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require 'vendor/autoload.php'; // Include Google's PHP SDK

require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $google_token = json_decode(file_get_contents("php://input"), true)['token'];

    // Verify Google token using Google's SDK
    $client = new Google_Client(['client_id' => '513931588844-2rjk6ukt0gc84gsho7f4epuu90p7o6up.apps.googleusercontent.com']);
    $payload = $client->verifyIdToken($google_token);

    if ($payload) {
        $email = $payload['email'];
        $first_name = $payload['given_name'];
        $last_name = $payload['family_name'];

        // Check if user already exists
        $sql = "SELECT * FROM userss WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Insert new Google user if not found
            $sql = "INSERT INTO userss (first_name, last_name, email) VALUES (?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sss", $first_name, $last_name, $email);
            $stmt->execute();
        }
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid Google token."]);
    }
}

$connection->close();

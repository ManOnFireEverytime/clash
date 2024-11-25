<?php
header('Access-Control-Allow-Origin: https://www.thevaultldn.com');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight (OPTIONS) request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'vendor/autoload.php'; // Ensure Google Client SDK is installed
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $google_token = $data['token'] ?? null;

    if (!$google_token) {
        echo json_encode(["success" => false, "message" => "No token provided."]);
        exit();
    }

    // Configure Google Client
    $client = new Google_Client(['client_id' => '513931588844-2rjk6ukt0gc84gsho7f4epuu90p7o6up.apps.googleusercontent.com']); // Replace with your Google Client ID
    $payload = $client->verifyIdToken($google_token);

    if ($payload) {
        $email = $payload['email'];
        $first_name = $payload['given_name'];
        $last_name = $payload['family_name'];

        // Check if the email exists in the database
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // If user doesn't exist, create a new user record
            $sql = "INSERT INTO users (first_name, last_name, email) VALUES (?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sss", $first_name, $last_name, $email);
            $stmt->execute();
        }

        // Send the user's first name and success message in the response
        echo json_encode([
            "success" => true,
            "message" => "Google Sign-In successful.",
            "first_name" => $first_name
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid Google token."]);
    }
} else {
    http_response_code(405); // Method Not Allowed for non-POST requests
    echo json_encode(["success" => false, "message" => "Only POST requests are allowed."]);
}

$connection->close();

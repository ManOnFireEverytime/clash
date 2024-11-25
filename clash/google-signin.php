<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require 'vendor/autoload.php'; // Include Google's PHP SDK and JWT library
require 'config/database.php'; // Include database connection

use \Firebase\JWT\JWT; // Add this to use the JWT library

$secret_key = "S3cReTkEyF0rJWTTok3n!123"; // Use your secret key

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
        } else {
            // Get user ID for JWT generation
            $user = $result->fetch_assoc();
        }

        // Create JWT payload
        $user_id = $user['id'];
        $token_payload = [
            "data" => [
                'id' => $user_id,
                'first_name' => $first_name,
                'email' => $email,
            ],
            'iat' => time(), // Issued at time
            'exp' => time() + (60 * 60) // Expiration time (1 hour from now)
        ];

        // Encode JWT
        $jwt = JWT::encode($token_payload, $secret_key, 'HS256');

        // Return the JWT to the frontend
        echo json_encode([
            "success" => true,
            "token" => $jwt,
            "first_name" => $first_name // Include the first name in the response
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid Google token."]);
    }

    $stmt->close();
    $connection->close();
}

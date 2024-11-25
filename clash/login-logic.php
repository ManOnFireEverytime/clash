<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}

use \Firebase\JWT\JWT;

require 'config/database.php';
require 'vendor/autoload.php';

$secret_key = "S3cReTkEyF0rJWTTok3n!123";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_username = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Query to check if the user exists
    $sql = "SELECT * FROM admin WHERE email = ? OR username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ss", $email_or_username, $email_or_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the user is verified
        if ($user['verified'] == 0) {
            echo json_encode([
                "success" => false,
                "message" => "Please verify your email before logging in."
            ]);
            exit();
        }

        if (password_verify($password, $user['passwd'])) {
            // Create a payload to include in the JWT
            $payload = [
                "data" => [           // Add this wrapper
                    "id" => $user['id'],
                    "username" => $user['username'], // Store the username
                    "email" => $user['email']
                ],
                "exp" => time() + (60 * 60)  // Token expires in 1 hour
            ];

            $jwt = JWT::encode($payload, $secret_key, 'HS256');

            echo json_encode([
                "success" => true,
                "token" => $jwt,
                "username" => $user['username'] // Include the username in the response
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Incorrect password."
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "User not found."
        ]);
    }

    $stmt->close();
}

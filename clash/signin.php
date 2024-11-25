<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and decode JSON data from request
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'];
    $password = $data['password'];

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Email and password are required."]);
        exit;
    }

    // Query to find user by email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // User found, verify password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password is correct, sign-in successful
            echo json_encode([
                "success" => true,
                "message" => "Sign-in successful",
                "first_name" => $user['first_name'] // Include first name in the response
            ]);
        } else {
            // Password incorrect
            echo json_encode(["success" => false, "message" => "Invalid email or password."]);
        }
    } else {
        // User not found
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
    }

    $stmt->close();
    $connection->close();
}

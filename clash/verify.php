<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}

require 'config/database.php'; // Include your database configuration

// Check if email is provided in the request
if (!isset($_POST['email'])) {
    echo json_encode(["success" => false, "message" => "Invalid access."]);
    exit();
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitize email input
$inputCode = filter_var($_POST['verification_code'], FILTER_SANITIZE_NUMBER_INT); // Sanitize input code

// Fetch the verification code from the database for this email
$stmt = $connection->prepare("SELECT verification FROM admin WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if ($row['verification'] == $inputCode) {
        // Update the user to set verified = 1
        $updateStmt = $connection->prepare("UPDATE admin SET verified = 1 WHERE email = ?");
        $updateStmt->bind_param("s", $email);
        if ($updateStmt->execute()) {
            echo json_encode(["success" => true, "message" => "Verification successful! You can now log in."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error verifying your account. Please try again."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid verification code. Please try again."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Email not found. Please check the email you used for registration."]);
}

$stmt->close();
$connection->close();

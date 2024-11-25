<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}

require 'config/database.php';

// Get JSON data from request body
$json_data = file_get_contents("php://input");
$data = json_decode($json_data);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data received"]);
    exit();
}

// Function to save base64 image

// Prepare and bind parameters for SQL insert
$sql = "INSERT INTO categories (name) VALUES (?)";

$stmt = $connection->prepare($sql);
if ($stmt === false) {
    echo json_encode(["status" => "error", "message" => "Failed to prepare statement: " . $connection->error]);
    exit();
}


// Bind parameters to the SQL query
$stmt->bind_param(
    "s",
    $data->category_name,
);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Category added successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to insert category data: " . $stmt->error
    ]);
}

// Close the statement and connection
$stmt->close();
$connection->close();

<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}

require 'config/database.php';

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate inputs
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Category ID is required."
    ]);
    exit();
}

if (!isset($data['name']) || empty(trim($data['name']))) {
    echo json_encode([
        "status" => "error",
        "message" => "Category name is required."
    ]);
    exit();
}

$categoryId = intval($_GET['id']);
$categoryName = trim($data['name']);

try {
    $stmt = $connection->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $categoryName, $categoryId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Category updated successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No changes made or category not found."
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update category: " . $e->getMessage()
    ]);
}

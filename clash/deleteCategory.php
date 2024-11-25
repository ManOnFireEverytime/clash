<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}

require 'config/database.php';

// Get category ID from URL parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "Category ID is required"
    ]);
    exit();
}

try {
    // Check if category exists and delete it
    $stmt = $connection->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "status" => "success",
                "message" => "Category deleted successfully"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Category not found"
            ]);
        }
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to delete category: " . $e->getMessage()
    ]);
} finally {
    $stmt->close();
    $connection->close();
}

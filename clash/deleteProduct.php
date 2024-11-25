<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require 'config/database.php';

// Decode JSON payload if method is POST
$requestMethod = $_SERVER['REQUEST_METHOD'];
if ($requestMethod === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $productId = isset($data['id']) ? intval($data['id']) : null;
} else {
    $productId = isset($_GET['id']) ? intval($_GET['id']) : null;
}

// Check if the product ID is provided
if (empty($productId)) {
    echo json_encode(["status" => "error", "message" => "Product ID is required"]);
    exit();
}

try {
    // Prepare the SQL statement to delete the product by its ID
    $stmt = $connection->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        // Check if any row was affected (product deleted)
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Product deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Product not found"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete product"]);
    }

    $stmt->close();
    $connection->close();
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "An error occurred: " . $e->getMessage()]);
    exit();
}
?>

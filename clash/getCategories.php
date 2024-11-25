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

try {
    // Query to fetch all categories ordered by name
    $sql = "SELECT id, name FROM categories ORDER BY name ASC";
    $result = $connection->query($sql);

    if ($result) {
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = [
                'id' => $row['id'],
                'name' => $row['name'],
            ];
        }
        echo json_encode([
            "status" => "success",
            "data" => $categories
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to fetch categories"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
} finally {
    $connection->close();
}

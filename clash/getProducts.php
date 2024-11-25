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

// Get the category ID from the request
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Prepare the SQL statement
$sql = "SELECT p.id, p.product_name, p.price, p.description, c.name AS category_name, p.image1
        FROM products p
        JOIN categories c ON p.category = c.id
        WHERE p.category = ?
        ORDER BY p.product_name";

// Prepare and bind
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $category_id);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Fetch the products
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        'id' => $row['id'],
        'product_name' => $row['product_name'],
        'price' => $row['price'],
        'description' => $row['description'],
        'category' => $row['category_name'],
        'image1' => $row['image1'], // Make sure your column name matches
    ];
}

// Close the statement and connection
$stmt->close();
$connection->close();

// Return the result
echo json_encode(['status' => 'success', 'data' => $products]);

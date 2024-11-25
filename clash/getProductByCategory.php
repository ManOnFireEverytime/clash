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

// Get the category from the query string
$category = isset($_GET['category']) ? $connection->real_escape_string($_GET['category']) : '';

// Prepare the SQL statement
$sql = "SELECT p.id, p.product_name, p.price, p.description,
               p.image1, p.image2, p.image3, p.image4, p.image5, 
               c.name
        FROM products p
        INNER JOIN categories c ON p.category = c.id
        WHERE c.name = '$category'";

// Execute the query
$result = $connection->query($sql);

// Check if products were found
if ($result->num_rows > 0) {
    $products = [];

    // Fetch all products
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // Return the results as JSON
    echo json_encode(["status" => "success", "data" => $products]);
} else {
    // No products found
    echo json_encode(["status" => "success", "data" => []]);
}

// Close the database connection
$connection->close();

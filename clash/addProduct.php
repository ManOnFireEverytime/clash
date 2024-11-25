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
function saveBase64Image($base64_image, $upload_dir)
{
    if (empty($base64_image)) return null;

    // Remove data URI scheme prefix
    if (strpos($base64_image, 'data:image/') === 0) {
        $base64_image = preg_replace('/^data:image\/\w+;base64,/', '', $base64_image);
    }

    $image_data = base64_decode($base64_image);
    if (!$image_data) return null;

    // Generate unique filename
    $filename = uniqid() . '.jpg';
    $file_path = $upload_dir . $filename;

    if (file_put_contents($file_path, $image_data)) {
        return $filename;
    }

    return null;
}

// Create upload directory if it doesn't exist
$upload_dir = 'products/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Save images
$imageNames = [];
$images = [
    $data->images[0] ?? null,
    $data->images[1] ?? null,
    $data->images[2] ?? null,
    $data->images[3] ?? null,
    $data->images[4] ?? null
];

foreach ($images as $base64_image) {
    $imageName = saveBase64Image($base64_image, $upload_dir);
    $imageNames[] = $imageName;
}

// Prepare and bind parameters for SQL insert
$sql = "INSERT INTO products 
    (product_name, category, price, description, image1, image2, image3, image4, image5)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $connection->prepare($sql);
if ($stmt === false) {
    echo json_encode(["status" => "error", "message" => "Failed to prepare statement: " . $connection->error]);
    exit();
}

$price = floatval($data->price);

$category = intval($data->category);

$stmt->bind_param(
    "sidssssss",
    $data->product_name,
    $category,
    $price,
    $data->description,
    $imageNames[0],
    $imageNames[1],
    $imageNames[2],
    $imageNames[3],
    $imageNames[4]
);


// Execute the statement
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Product added successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to insert product data: " . $stmt->error
    ]);
}

// Close the statement and connection
$stmt->close();
$connection->close();
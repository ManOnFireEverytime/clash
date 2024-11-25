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

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents("php://input"));

// Validate input
if (!$data || !isset($data->id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// Prepare data for update
$id = $data->id;
$product_name = $connection->real_escape_string($data->product_name);
$category = $data->category;
$price = $data->price;
$description = $connection->real_escape_string($data->description);

// Get existing images
$oldImagesQuery = "SELECT image1, image2, image3, image4, image5 FROM products WHERE id = $id";
$oldImagesResult = $connection->query($oldImagesQuery);
$oldImages = $oldImagesResult->fetch_assoc();

// Function to save base64 image
function saveBase64Image($base64_image, $upload_dir)
{
    if (empty($base64_image)) return null;

    // Check if it's already a filename (not a base64 string)
    if (!preg_match('/^data:image\//', $base64_image)) {
        return $base64_image; // Return existing filename
    }

    // Process new base64 image
    $base64_image = preg_replace('/^data:image\/\w+;base64,/', '', $base64_image);
    $image_data = base64_decode($base64_image);

    if (!$image_data) return null;

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

// Initialize image names array with existing images
$imageNames = [
    $oldImages['image1'],
    $oldImages['image2'],
    $oldImages['image3'],
    $oldImages['image4'],
    $oldImages['image5']
];

// Process new images
$newImages = [
    $data->image1 ?? null,
    $data->image2 ?? null,
    $data->image3 ?? null,
    $data->image4 ?? null,
    $data->image5 ?? null
];

// Update only the images that have new data
foreach ($newImages as $index => $newImage) {
    if ($newImage) {
        // Only process if it's a new image (base64 string)
        if (preg_match('/^data:image\//', $newImage)) {
            // Delete old image if it exists
            if ($imageNames[$index]) {
                $oldImagePath = $upload_dir . $imageNames[$index];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            // Save new image
            $imageName = saveBase64Image($newImage, $upload_dir);
            $imageNames[$index] = $imageName;
        }
    }
}

// Prepare update query with proper null handling
$updateQuery = "UPDATE products SET 
    product_name = '$product_name', 
    category = '$category', 
    price = '$price', 
    description = '$description', 
    image1 = " . ($imageNames[0] ? "'{$imageNames[0]}'" : 'NULL') . ", 
    image2 = " . ($imageNames[1] ? "'{$imageNames[1]}'" : 'NULL') . ", 
    image3 = " . ($imageNames[2] ? "'{$imageNames[2]}'" : 'NULL') . ", 
    image4 = " . ($imageNames[3] ? "'{$imageNames[3]}'" : 'NULL') . ", 
    image5 = " . ($imageNames[4] ? "'{$imageNames[4]}'" : 'NULL') . " 
WHERE id = $id";

// Execute update
if ($connection->query($updateQuery) === TRUE) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Product updated successfully',
        'images' => $imageNames // Return updated image names for debugging
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error updating product: ' . $connection->error,
        'query' => $updateQuery // For debugging
    ]);
}

$connection->close();

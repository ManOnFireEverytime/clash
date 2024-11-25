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

$result = mysqli_query($connection, "SELECT * FROM products ORDER BY id DESC");

if ($result) {
    $products = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $products
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => mysqli_error($connection)
    ]);
}

mysqli_close($connection);

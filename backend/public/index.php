<?php

// Swagger docs
if (strpos($_SERVER['REQUEST_URI'], '/v1/docs') === 0) {
    require __DIR__ . '/v1/docs/swagger.php';
    exit;
}

// REST API
if (strpos($_SERVER['REQUEST_URI'], '/rest') === 0 ||
    strpos($_SERVER['REQUEST_URI'], '/auth') === 0 ||
    strpos($_SERVER['REQUEST_URI'], '/cars') === 0 ||
    strpos($_SERVER['REQUEST_URI'], '/locations') === 0
) {
    require __DIR__ . '/../index.php';
    exit;
}

// Health check
header('Content-Type: application/json');
echo json_encode([
    "status" => "OK",
    "service" => "Car Rental API"
]);

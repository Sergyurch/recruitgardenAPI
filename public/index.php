<?php

use Dotenv\Dotenv;
use FarmAPI\Router;

try {
    // Load Composer's autoloader
    require_once __DIR__ . '/../vendor/autoload.php';

    // Load environment variables from .env file
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    // Set the response type to JSON
    header('Content-Type: application/json');

    // Initialize the router and handle incoming requests
    $router = new Router();
    $router->handleRequest();
} catch (Throwable $e) {
    // Log the exception message for debugging purposes
    error_log("Error: " . $e->getMessage());

    // Send a generic 500 Internal Server Error response
    http_response_code(500);
    echo json_encode(['error' => 'Internal error'], JSON_UNESCAPED_UNICODE);
}

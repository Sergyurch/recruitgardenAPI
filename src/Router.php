<?php

namespace FarmAPI;

class Router {
    private $productController;

    // Initializes the ProductController
    public function __construct() {
        $this->productController = new ProductController();
    }

    // Handles the incoming request and routes it to the appropriate controller method
    public function handleRequest() {
        // Get the HTTP request method
        $method = $_SERVER['REQUEST_METHOD'];

        // Parse the request URI to get the path
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $id = null;

        // Check if the URL path contains a product ID (e.g., /products/1)
        if (preg_match('/\/products\/(\d+)/', $path, $matches)) {
            $id = $matches[1];

            // Check a product with the provided id exists
            if (!$this->productController->exists($id)) {
                http_response_code(404);
                echo json_encode(['error' => 'Product doesn\'t exist'], JSON_UNESCAPED_UNICODE);

                return;
            }
        }

        if ($path === '/products' && $method === 'GET') { // Handle GET requests to retrieve all products
            echo json_encode($this->productController->getAll($_GET), JSON_UNESCAPED_UNICODE);
        } elseif ($id && $method === 'GET') { // Handle GET requests to retrieve a single product by ID
            echo json_encode($this->productController->getOne($id), JSON_UNESCAPED_UNICODE);
        } elseif ($path === '/products' && $method === 'POST') { // Handle POST requests to create a new product
            // Get the raw POST data and decode it
            $data = json_decode(file_get_contents('php://input'), true);

            // Check for JSON parsing errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->sendUnsupportedMediaTypeError();
                
                return;
            }

            // Create new product
            $result = $this->productController->create($data);

            if ($result) {
                http_response_code(201); // Resource created successfully
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            } else {
                throw new \PDOException('Failed to create product');
            }
        } elseif ($id && $method === 'PATCH') { // Handle PATCH requests to update an existing product by ID
            // Get the raw PATCH data and decode it
            $data = json_decode(file_get_contents('php://input'), true);

            // Check for JSON parsing errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->sendUnsupportedMediaTypeError();
                
                return;
            }

            // Update the product and return the result as JSON
            echo json_encode($this->productController->update($id, $data), JSON_UNESCAPED_UNICODE);
        } elseif ($id && $method === 'DELETE') { // Handle DELETE requests to delete a product by ID
            // Delete the product and return the result as JSON
            echo json_encode(['deleted_records' => $this->productController->delete($id)]);
        } else { // Return a 404 Not Found error for unsupported routes
            http_response_code(404);
            echo json_encode(['error' => 'Not Found'], JSON_UNESCAPED_UNICODE);
        }
    }

    // Sends a 415 Unsupported Media Type error when the client sends invalid or non-JSON data
    private function sendUnsupportedMediaTypeError() {
        http_response_code(415);
        echo json_encode([
            'error' => 'Unsupported Media Type Or Incorrect JSON',
            "message" => "This API expects JSON. Please set the 'Content-Type' header to 'application/json' and send data in JSON format."
        ], JSON_UNESCAPED_UNICODE);
    }
}

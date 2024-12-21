<?php

namespace FarmAPI;

use PDO;

class ProductController {
    private $db;

    // Constructor to initialize the database connection
    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Fetch all products with optional filters
    public function getAll($filters = []) {
        // Base query to fetch all products
        $query = "SELECT * FROM farm_products WHERE 1=1";
        $params = [];

        // Apply filters for 'name' if provided
        if (!empty($filters['name'])) {
            $query .= " AND name ILIKE ?";
            $params[] = '%' . $filters['name'] . '%';
        }

        // Prepare and execute the query
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        // Return the result as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a single product by ID
    public function getOne($id) {
        // Query to fetch a product by ID
        $query = "SELECT * FROM farm_products WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the result or an empty array if the product does not exist
        return $result ? $result : [];
    }

    // Create a new product
    public function create($data) {
        // Validate the product data
        $errors = Validator::validateProductData($data);

        if (!empty($errors)) {
            // Return validation errors if any
            return ['errors' => $errors];
        }

        // Query to insert a new product and return the created record
        $query = "INSERT INTO farm_products (name, quantity, price) VALUES (?, ?, ?) RETURNING *";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$data['name'], $data['quantity'], $data['price']]);

        // Return the newly created product
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update an existing product by ID (partial update is allowed)
    public function update($id, $data) {
        // Validate the product data
        $errors = Validator::validateProductData($data, false);

        if (!empty($errors)) {
            // Return validation errors if any
            return ['errors' => $errors];
        }

        // Build the SET clause dynamically based on provided data
        $setData = [];
        $params = [];

        if (isset($data['name']))  {
            $setData[] = 'name = ?';
            $params[] = $data['name'];
        }

        if (isset($data['quantity']))  {
            $setData[] = 'quantity = ?';
            $params[] = $data['quantity'];
        }

        if (isset($data['price']))  {
            $setData[] = 'price = ?';
            $params[] = $data['price'];
        }

        // Add the product ID as the last parameter
        $params[] = $id;

        // Query to update the product and return the updated record
        $query = "UPDATE farm_products SET " . implode(',', $setData) . " WHERE id = ? RETURNING *";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        // Return the updated product
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Delete a product by ID
    public function delete($id) {
        // Query to delete a product by ID
        $stmt = $this->db->prepare("DELETE FROM farm_products WHERE id = ?");
        $stmt->execute([$id]);

        // Return the number of deleted rows
        return $stmt->rowCount();
    }

    // Check if a product exists by ID
    public function exists($id) {
        // Query to check for the existence of a product by ID
        $query = "SELECT COUNT(*) FROM farm_products WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        // Return true if the count is greater than 0, false otherwise
        return $count > 0;
    }
}

<?php

namespace FarmAPI;

class Validator {
    public static function validateProductData($data, $checkAll = true) {
        $errors = [];

        // For the case when we update only some fields of the product
        if (!$checkAll && !isset($data['name']) && !isset($data['quantity']) && !isset($data['price'])) {
            $errors[] = "Invalid data provided.";
            
            return $errors;
        }
        
        // Checks that product name is provided and valid
        if ($checkAll || isset($data['name'])) {
            if (empty($data['name']) || strlen($data['name']) > 255) {
                $errors[] = "Invalid product name.";
            }
        }

        // Checks that product quantity is provided and valid
        if ($checkAll || isset($data['quantity'])) {
            if (!isset($data['quantity']) || !is_numeric($data['quantity']) || $data['quantity'] < 0) {
                $errors[] = "Quantity must be a non-negative number.";
            }
        }

        // Checks that product price is provided and valid
        if ($checkAll || isset($data['price'])) {
            if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
                $errors[] = "Price must be a non-negative number.";
            }
        }

        return $errors;
    }
}

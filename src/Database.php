<?php

namespace FarmAPI;

use PDO;

class Database {
    // Static variable to hold the single instance of the PDO connection
    private static $instance = null;

    public static function getConnection() {
        // If there is no existing connection
        if (self::$instance === null) {
            $dsn = "pgsql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}";

            // Create a new PDO instance to establish the database connection
            self::$instance = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

            // Set the error mode for the PDO instance to throw exceptions
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        // Return the existing PDO instance
        return self::$instance;
    }
}

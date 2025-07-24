<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    // Try to connect to MySQL without selecting a database
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if database exists
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'food_delivery'");
    $dbExists = $stmt->fetch();

    if (!$dbExists) {
        // Database doesn't exist, create it
        $pdo->exec("CREATE DATABASE IF NOT EXISTS food_delivery");
        $pdo->exec("USE food_delivery");
        
        // Read and execute SQL file
        $sql = file_get_contents('../database.sql');
        $queries = explode(';', $sql);
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }
        
        echo json_encode(array(
            "status" => "success",
            "message" => "Database created and schema imported successfully"
        ));
    } else {
        // Database exists, check tables
        $pdo->exec("USE food_delivery");
        $tables = array('users', 'dishes', 'orders', 'order_items');
        $missingTables = array();
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() == 0) {
                $missingTables[] = $table;
            }
        }
        
        if (empty($missingTables)) {
            echo json_encode(array(
                "status" => "success",
                "message" => "Database and tables exist"
            ));
        } else {
            // Import schema if tables are missing
            $sql = file_get_contents('../database.sql');
            $queries = explode(';', $sql);
            
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $pdo->exec($query);
                }
            }
            
            echo json_encode(array(
                "status" => "success",
                "message" => "Missing tables created: " . implode(", ", $missingTables)
            ));
        }
    }
} catch (PDOException $e) {
    http_response_code(503);
    echo json_encode(array(
        "status" => "error",
        "message" => "Database error",
        "error" => $e->getMessage()
    ));
}
?> 
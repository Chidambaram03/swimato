<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// Debug log
error_log("Received order data: " . print_r($data, true));

if (
    !empty($data->user_id) &&
    !empty($data->items) &&
    !empty($data->total_amount) &&
    !empty($data->payment_method)
) {
    try {
        $db->beginTransaction();

        // Create order
        $query = "INSERT INTO orders (user_id, total_amount, payment_method, status) VALUES (?, ?, ?, 'pending')";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $data->user_id,
            $data->total_amount,
            $data->payment_method
        ]);
        
        $order_id = $db->lastInsertId();
        error_log("Created order with ID: " . $order_id);

        // Insert order items
        $query = "INSERT INTO order_items (order_id, dish_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);

        foreach ($data->items as $item) {
            error_log("Processing item: " . print_r($item, true));
            
            if (empty($item->dish_id) || empty($item->quantity) || empty($item->price)) {
                throw new Exception("Invalid item data: " . print_r($item, true));
            }
            
            $stmt->execute([
                $order_id,
                $item->dish_id,
                $item->quantity,
                $item->price
            ]);
            error_log("Inserted order item for dish_id: " . $item->dish_id);
        }

        $db->commit();
        error_log("Order transaction committed successfully");

        http_response_code(201);
        echo json_encode(array(
            "status" => "success",
            "message" => "Order created successfully.",
            "order_id" => $order_id
        ));
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Order error: " . $e->getMessage());
        http_response_code(503);
        echo json_encode(array(
            "status" => "error",
            "message" => "Unable to create order",
            "error" => $e->getMessage()
        ));
    }
} else {
    error_log("Incomplete order data received: " . print_r($data, true));
    http_response_code(400);
    echo json_encode(array(
        "status" => "error",
        "message" => "Unable to create order. Data is incomplete.",
        "required_fields" => ["user_id", "items", "total_amount", "payment_method"]
    ));
}
?> 
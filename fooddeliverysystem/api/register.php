<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate input
if (
    !empty($data->name) &&
    !empty($data->email) &&
    !empty($data->password) &&
    !empty($data->address) &&
    !empty($data->phone)
) {
    try {
        // Check if email exists
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$data->email]);

        if ($stmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(array("message" => "Email already exists."));
            exit();
        }

        // Hash password
        $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);

        // Insert user
        $query = "INSERT INTO users (name, email, password, address, phone) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);

        if ($stmt->execute([$data->name, $data->email, $hashed_password, $data->address, $data->phone])) {
            http_response_code(201);
            echo json_encode(array("message" => "User registered successfully."));
        } else {
            throw new Exception("Failed to execute insert query");
        }
    } catch (Exception $e) {
        http_response_code(503);
        echo json_encode(array(
            "message" => "Unable to register user.",
            "error" => $e->getMessage()
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "message" => "Unable to register user. Data is incomplete.",
        "received_data" => $data
    ));
}
?> 